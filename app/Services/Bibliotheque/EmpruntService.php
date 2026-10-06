<?php

namespace App\Services\Bibliotheque;

use App\Enums\EtatPhysique;
use App\Enums\EvenementEmprunt;
use App\Enums\NiveauAcces;
use App\Enums\StatutEmprunt;
use App\Enums\StatutExemplaire;
use App\Enums\TypeIncident;
use App\Exceptions\BibliothequeException;
use App\Models\Adherent;
use App\Models\Document;
use App\Models\Emprunt;
use App\Models\Exemplaire;
use App\Models\Incident;
use App\Repositories\Contracts\AdherentRepositoryInterface;
use App\Repositories\Contracts\EmpruntRepositoryInterface;
use App\Repositories\Contracts\ExemplaireRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * Circulation des exemplaires : prêts, retours, pertes, demandes en ligne et échéances.
 *
 * Règles appliquées :
 *  - un exemplaire n'est prêté que s'il est disponible (ou réservé pour cet adhérent) ;
 *  - un exemplaire n'a qu'un seul prêt actif (contrôlé ici et par l'index unique `exemplaire_actif_id`) ;
 *  - un adhérent suspendu ou expiré n'emprunte pas, ni au-delà de son quota ;
 *  - un exemplaire retourné redevient disponible, sauf s'il est endommagé ou retiré ;
 *  - un exemplaire perdu ne peut plus être prêté.
 */
class EmpruntService
{
    public function __construct(
        private readonly EmpruntRepositoryInterface $emprunts,
        private readonly ExemplaireRepositoryInterface $exemplaires,
        private readonly AdherentRepositoryInterface $adherents,
        private readonly ParametresPret $parametres,
        private readonly NotificateurBibliotheque $notificateur,
        private readonly Journal $journal,
    ) {
    }

    /* ---------------------------------------------------------------
     | Guichet
     * ------------------------------------------------------------- */

    /**
     * Enregistre un prêt au guichet à partir du matricule de l'adhérent et du code de l'exemplaire.
     *
     * @throws BibliothequeException
     */
    public function preter(string $matricule, string $codeExemplaire): Emprunt
    {
        $adherent = $this->adherents->parMatricule($matricule)
            ?? throw new BibliothequeException('Aucun adhérent ne correspond à ce matricule.');
        $exemplaire = $this->exemplaires->parCode($codeExemplaire)
            ?? throw new BibliothequeException('Aucun exemplaire ne porte ce code d\'inventaire ou ce code-barres.');
        $document = $exemplaire->document;

        if ($document->consultation_sur_place) {
            throw new BibliothequeException('Ce document est réservé à la consultation sur place.');
        }

        $emprunt = DB::transaction(function () use ($adherent, $exemplaire, $document) {
            $exemplaire = $this->exemplaires->verrouiller($exemplaire);
            // Une demande en ligne de cet adhérent pour ce document est servie plutôt que dupliquée.
            $existante = $this->emprunts->actifPourDocument($adherent, $document);

            if ($existante?->statut === StatutEmprunt::EnCours) {
                throw new BibliothequeException("{$adherent->nom_complet} a déjà un exemplaire de ce document en prêt.");
            }

            $reservePourLui = $existante && (int) $existante->exemplaire_id === (int) $exemplaire->id;
            $this->verifierExemplaire($exemplaire, $reservePourLui);

            if ($existante) {
                $this->verifierAdherent($adherent, compterDemande: false);
                if ($existante->exemplaire && ! $reservePourLui) {
                    $this->liberer($existante->exemplaire);
                }
                $emprunt = $existante;
            } else {
                $this->verifierAdherent($adherent);
                $emprunt = $this->emprunts->create([
                    'adherent_id' => $adherent->id,
                    'document_id' => $document->id,
                    'statut' => StatutEmprunt::Demande,
                    'canal' => 'guichet',
                ]);
            }

            return $this->ouvrirPret($emprunt, $exemplaire, $adherent);
        });

        $this->notificateur->adherent($emprunt, EvenementEmprunt::Remis);

        return $emprunt;
    }

    /**
     * Retour d'un exemplaire. L'état constaté met à jour la fiche de l'exemplaire :
     * endommagé ⇒ en réparation (+ incident), retiré ⇒ retiré du catalogue, sinon remis en rayon.
     *
     * @throws BibliothequeException
     */
    public function retourner(Emprunt $emprunt, EtatPhysique $etat, bool $retirer = false, ?string $note = null): Emprunt
    {
        $this->exigerStatut($emprunt, StatutEmprunt::EnCours);

        DB::transaction(function () use ($emprunt, $etat, $retirer, $note) {
            $retard = $emprunt->joursDeRetard();
            $exemplaire = $emprunt->exemplaire;

            $this->emprunts->update($emprunt, [
                'statut' => StatutEmprunt::Rendu,
                'date_retour' => now(),
                'jours_retard' => $retard,
                'etat_retour' => $etat,
                'exemplaire_actif_id' => null,
                'traite_par' => auth()->id(),
            ]);

            $exemplaire->etat_physique = $etat;
            if ($retirer) {
                $exemplaire->statut = StatutExemplaire::Retire;
            } elseif ($etat === EtatPhysique::Endommage) {
                $exemplaire->statut = StatutExemplaire::EnReparation;
            }
            $exemplaire->save();

            if ($etat === EtatPhysique::Endommage) {
                $this->incident($emprunt, TypeIncident::Dommage, $note ?: 'Exemplaire rendu endommagé.');
            }
            if ($retard > 0) {
                $this->incident($emprunt, TypeIncident::Retard, "Retour avec {$retard} jour(s) de retard.");
            }

            if (! $retirer && $etat !== EtatPhysique::Endommage) {
                $this->liberer($exemplaire);
            }

            $this->journal->enregistrer('retour', $emprunt,
                "Retour de {$exemplaire->code_inventaire} par {$emprunt->adherent->nom_complet}".($retard ? " ({$retard} j de retard)" : ''),
                array_filter(['etat' => $etat->value, 'retard' => $retard, 'retire' => $retirer ?: null, 'note' => $note]),
                $emprunt->adherent);
        });

        $this->notificateur->adherent($emprunt, EvenementEmprunt::Rendu);

        return $emprunt;
    }

    /** Retour à partir du code de l'exemplaire (douchette ou saisie). @throws BibliothequeException */
    public function empruntEnCoursPourCode(string $code): Emprunt
    {
        $exemplaire = $this->exemplaires->parCode($code)
            ?? throw new BibliothequeException('Aucun exemplaire ne porte ce code.');

        return $exemplaire->empruntActif
            ?? throw new BibliothequeException("L'exemplaire {$exemplaire->code_inventaire} n'est pas en prêt ({$exemplaire->statut->libelle()}).");
    }

    /** L'exemplaire prêté est déclaré perdu : le prêt est clos, l'exemplaire ne peut plus circuler. @throws BibliothequeException */
    public function declarerPerte(Emprunt $emprunt, ?string $note = null): Emprunt
    {
        $this->exigerStatut($emprunt, StatutEmprunt::EnCours);

        DB::transaction(function () use ($emprunt, $note) {
            $this->emprunts->update($emprunt, [
                'statut' => StatutEmprunt::Perdu,
                'date_retour' => now(),
                'exemplaire_actif_id' => null,
                'motif' => $note,
                'traite_par' => auth()->id(),
            ]);
            $emprunt->exemplaire->update(['statut' => StatutExemplaire::Perdu]);
            $this->incident($emprunt, TypeIncident::Perte, $note ?: 'Exemplaire déclaré perdu pendant le prêt.');
            $this->journal->enregistrer('perte', $emprunt,
                "Perte de {$emprunt->exemplaire->code_inventaire} déclarée pour {$emprunt->adherent->nom_complet}",
                array_filter(['note' => $note]), $emprunt->adherent);
        });

        return $emprunt;
    }

    /* ---------------------------------------------------------------
     | Demandes en ligne (espace adhérent)
     * ------------------------------------------------------------- */

    /** @throws BibliothequeException */
    public function demander(Adherent $adherent, Document $document, ?string $message = null): Emprunt
    {
        if (! $this->demandable($document)) {
            throw new BibliothequeException($document->consultation_sur_place
                ? 'Ce document est réservé à la consultation sur place.'
                : 'Ce document ne peut pas être demandé en ligne.');
        }

        $this->verifierAdherent($adherent);
        if ($this->emprunts->actifPourDocument($adherent, $document)) {
            throw new BibliothequeException('Vous avez déjà une demande ou un prêt en cours pour ce document.');
        }

        $emprunt = $this->emprunts->create([
            'adherent_id' => $adherent->id,
            'document_id' => $document->id,
            'statut' => StatutEmprunt::Demande,
            'canal' => 'en_ligne',
            'message' => $message,
        ]);

        $this->journal->enregistrer('demande', $emprunt, "Demande en ligne de « {$document->titre} » par {$adherent->nom_complet}", [], $adherent);
        $this->notificateur->adherent($emprunt, EvenementEmprunt::Demande);
        $this->notificateur->bibliotheque($emprunt);

        return $emprunt;
    }

    /**
     * Un document se demande en ligne s'il est prêtable, a des exemplaires en circulation
     * et n'est pas déjà proposé en version numérique ouverte aux adhérents.
     */
    public function demandable(Document $document): bool
    {
        return ! $document->consultation_sur_place
            && $document->exemplaires()->whereIn('statut', [
                StatutExemplaire::Disponible->value, StatutExemplaire::Emprunte->value, StatutExemplaire::Reserve->value,
            ])->exists()
            && ! $document->ressources()->where('niveau_acces', '!=', NiveauAcces::Restreint->value)->exists();
    }

    /** Met un exemplaire de côté pour l'adhérent. @throws BibliothequeException */
    public function valider(Emprunt $emprunt): Emprunt
    {
        $this->exigerStatut($emprunt, StatutEmprunt::Demande);

        $reserve = DB::transaction(function () use ($emprunt) {
            $exemplaire = $this->exemplaires->disponiblePour($emprunt->document);
            if (! $exemplaire) {
                return false;
            }
            $this->reserver($emprunt, $exemplaire);

            return true;
        });

        if (! $reserve) {
            throw new BibliothequeException('Aucun exemplaire n\'est disponible pour le moment. La demande reste en file d\'attente et sera servie au prochain retour.');
        }

        $this->notificateur->adherent($emprunt, EvenementEmprunt::Reserve);

        return $emprunt;
    }

    /** Remise au guichet d'un document demandé en ligne. @throws BibliothequeException */
    public function remettre(Emprunt $emprunt): Emprunt
    {
        if (! $emprunt->peutEtreAnnule()) {
            throw new BibliothequeException('Ce document a déjà été remis ou la demande est close.');
        }

        DB::transaction(function () use ($emprunt) {
            $this->verifierAdherent($emprunt->adherent, compterDemande: false);
            $exemplaire = $emprunt->exemplaire
                ? $this->exemplaires->verrouiller($emprunt->exemplaire)
                : $this->exemplaires->disponiblePour($emprunt->document);
            if (! $exemplaire) {
                throw new BibliothequeException('Aucun exemplaire disponible à remettre.');
            }
            $this->ouvrirPret($emprunt, $exemplaire, $emprunt->adherent);
        });

        $this->notificateur->adherent($emprunt, EvenementEmprunt::Remis);

        return $emprunt;
    }

    /** @throws BibliothequeException */
    public function refuser(Emprunt $emprunt, string $motif): Emprunt
    {
        return $this->clore($emprunt, StatutEmprunt::Refuse, $motif, EvenementEmprunt::Refuse);
    }

    /** Annulation par l'adhérent. @throws BibliothequeException */
    public function annuler(Emprunt $emprunt): Emprunt
    {
        return $this->clore($emprunt, StatutEmprunt::Annule, 'Annulée par l\'adhérent.');
    }

    /** @throws BibliothequeException */
    public function prolonger(Emprunt $emprunt): Emprunt
    {
        if (! $emprunt->peutEtreProlonge()) {
            throw new BibliothequeException($emprunt->estEnRetard()
                ? 'Un document en retard ne peut pas être prolongé : merci de le rapporter.'
                : 'Ce prêt ne peut plus être prolongé.');
        }

        if ($this->emprunts->compterDemandes($emprunt->document) > 0) {
            throw new BibliothequeException('D\'autres adhérents attendent ce document : la prolongation n\'est pas possible.');
        }

        $echeance = $emprunt->date_retour_prevue->copy()->addDays($this->parametres->dureePret($emprunt->adherent->type));
        $this->emprunts->update($emprunt, [
            'date_retour_prevue' => $echeance,
            'prolongations' => $emprunt->prolongations + 1,
            'rappel_envoye_le' => null,
        ]);

        $this->journal->enregistrer('prolongation', $emprunt,
            "Prolongation du prêt de {$emprunt->exemplaire->code_inventaire} jusqu'au {$echeance->format('d/m/Y')}", [], $emprunt->adherent);
        $this->notificateur->adherent($emprunt, EvenementEmprunt::Prolonge);

        return $emprunt;
    }

    /* ---------------------------------------------------------------
     | Tâche quotidienne
     * ------------------------------------------------------------- */

    /**
     * Expire les adhérents échus, annule les réservations non retirées, envoie rappels et relances.
     *
     * @return array{adherents:int, expirees:int, rappels:int, relances:int}
     */
    public function traiterEcheances(): array
    {
        $bilan = ['adherents' => $this->adherents->expirerEchus(), 'expirees' => 0, 'rappels' => 0, 'relances' => 0];

        foreach ($this->emprunts->reservationsExpirees() as $emprunt) {
            $this->clore($emprunt, StatutEmprunt::Annule, 'Document non retiré dans le délai.', EvenementEmprunt::Expire);
            $bilan['expirees']++;
        }

        foreach ($this->emprunts->aRappeler($this->parametres->rappelAvantEcheance()) as $emprunt) {
            if ($this->notificateur->adherent($emprunt, EvenementEmprunt::Rappel)) {
                $this->emprunts->update($emprunt, ['rappel_envoye_le' => now()]);
                $bilan['rappels']++;
            }
        }

        foreach ($this->emprunts->aRelancer($this->parametres->relanceTousLes()) as $emprunt) {
            if ($this->notificateur->adherent($emprunt, EvenementEmprunt::Retard)) {
                $this->emprunts->update($emprunt, ['derniere_relance_le' => now()]);
                $bilan['relances']++;
            }
        }

        return $bilan;
    }

    /* ---------------------------------------------------------------
     | Interne
     * ------------------------------------------------------------- */

    /** @throws BibliothequeException */
    private function verifierAdherent(Adherent $adherent, bool $compterDemande = true): void
    {
        $statut = $adherent->statutEffectif();
        if (! $adherent->estActif()) {
            throw new BibliothequeException("L'adhérent {$adherent->nom_complet} est « {$statut->libelle()} » : aucun nouvel emprunt n'est possible.");
        }

        if ($this->emprunts->aDesRetards($adherent)) {
            throw new BibliothequeException("{$adherent->nom_complet} a un document en retard : il doit être rendu avant tout nouvel emprunt.");
        }

        // Une demande déjà comptée dans le quota ne bloque pas sa propre remise.
        $max = $this->parametres->maxPrets($adherent->type);
        $actifs = $this->emprunts->compterActifs($adherent) - ($compterDemande ? 0 : 1);
        if ($actifs >= $max) {
            throw new BibliothequeException("Limite atteinte : {$max} prêts ou demandes simultanés pour un adhérent « {$adherent->type->libelle()} ».");
        }
    }

    /** @throws BibliothequeException */
    private function verifierExemplaire(Exemplaire $exemplaire, bool $reservePourLui = false): void
    {
        if ($exemplaire->empruntActif()->exists()) {
            throw new BibliothequeException("L'exemplaire {$exemplaire->code_inventaire} est déjà en prêt.");
        }

        if (! $exemplaire->estPretable() && ! ($reservePourLui && $exemplaire->statut === StatutExemplaire::Reserve)) {
            throw new BibliothequeException("L'exemplaire {$exemplaire->code_inventaire} ne peut pas être prêté : {$exemplaire->statut->libelle()}.");
        }
    }

    /** @throws BibliothequeException */
    private function exigerStatut(Emprunt $emprunt, StatutEmprunt $attendu): void
    {
        if ($emprunt->statut !== $attendu) {
            throw new BibliothequeException("Opération impossible : ce prêt est « {$emprunt->statut->libelle()} ».");
        }
    }

    private function ouvrirPret(Emprunt $emprunt, Exemplaire $exemplaire, Adherent $adherent): Emprunt
    {
        $exemplaire->update(['statut' => StatutExemplaire::Emprunte]);

        $this->emprunts->update($emprunt, [
            'statut' => StatutEmprunt::EnCours,
            'exemplaire_id' => $exemplaire->id,
            'exemplaire_actif_id' => $exemplaire->id,
            'date_pret' => now(),
            'date_retour_prevue' => today()->addDays($this->parametres->dureePret($adherent->type)),
            'retirer_avant' => null,
            'traite_par' => auth()->id(),
        ]);

        $this->journal->enregistrer('pret', $emprunt,
            "Prêt de {$exemplaire->code_inventaire} (« {$emprunt->document->titre} ») à {$adherent->nom_complet}, retour prévu le {$emprunt->date_retour_prevue->format('d/m/Y')}",
            [], $adherent);

        return $emprunt;
    }

    private function reserver(Emprunt $emprunt, Exemplaire $exemplaire): void
    {
        $exemplaire->update(['statut' => StatutExemplaire::Reserve]);
        $this->emprunts->update($emprunt, [
            'statut' => StatutEmprunt::Reserve,
            'exemplaire_id' => $exemplaire->id,
            'retirer_avant' => today()->addDays($this->parametres->delaiRetrait()),
            'traite_par' => auth()->id() ?? $emprunt->traite_par,
        ]);

        $this->journal->enregistrer('demande.traitement', $emprunt,
            "Exemplaire {$exemplaire->code_inventaire} mis de côté pour {$emprunt->adherent->nom_complet}", [], $emprunt->adherent);
    }

    /** Clôt une demande ou une réservation et libère l'exemplaire éventuellement mis de côté. */
    private function clore(Emprunt $emprunt, StatutEmprunt $statut, string $motif, ?EvenementEmprunt $evenement = null): Emprunt
    {
        if (! $emprunt->peutEtreAnnule()) {
            throw new BibliothequeException('Seule une demande en attente ou une réservation peut être annulée ou refusée.');
        }

        DB::transaction(function () use ($emprunt, $statut, $motif) {
            $exemplaire = $emprunt->exemplaire;
            $this->emprunts->update($emprunt, ['statut' => $statut, 'motif' => $motif, 'exemplaire_id' => null, 'traite_par' => auth()->id() ?? $emprunt->traite_par]);
            if ($exemplaire) {
                $this->liberer($exemplaire);
            }
            $this->journal->enregistrer('demande.traitement', $emprunt,
                "Demande de « {$emprunt->document->titre} » ({$emprunt->adherent->nom_complet}) : {$statut->libelle()}",
                ['motif' => $motif], $emprunt->adherent);
        });

        if ($evenement) {
            $this->notificateur->adherent($emprunt, $evenement);
        }

        return $emprunt;
    }

    /** Remet l'exemplaire en rayon, ou le met de côté pour la plus ancienne demande en attente. */
    private function liberer(Exemplaire $exemplaire): void
    {
        $suivante = $this->emprunts->prochaineDemande($exemplaire->document);

        if (! $suivante) {
            $exemplaire->update(['statut' => StatutExemplaire::Disponible]);

            return;
        }

        $this->reserver($suivante, $exemplaire);
        DB::afterCommit(fn () => $this->notificateur->adherent($suivante, EvenementEmprunt::Reserve));
    }

    private function incident(Emprunt $emprunt, TypeIncident $type, ?string $description): void
    {
        Incident::create([
            'exemplaire_id' => $emprunt->exemplaire_id,
            'emprunt_id' => $emprunt->id,
            'adherent_id' => $emprunt->adherent_id,
            'type' => $type,
            'description' => $description,
            'user_id' => auth()->id(),
        ]);
    }
}
