<?php

namespace App\Services\Bibliotheque;

use App\Enums\EtatExemplaire;
use App\Enums\EvenementEmprunt;
use App\Enums\StatutEmprunt;
use App\Exceptions\BibliothequeException;
use App\Models\Document;
use App\Models\Emprunt;
use App\Models\Exemplaire;
use App\Models\Inscription;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use App\Repositories\Contracts\EmpruntRepositoryInterface;
use App\Repositories\Contracts\InscriptionRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * Règlement de prêt de la bibliothèque.
 *
 * Demande → Réservé (exemplaire mis de côté, à retirer avant N jours) → En cours → Rendu.
 * Quand un exemplaire redevient disponible, il est attribué à la plus ancienne demande en attente.
 */
class EmpruntService
{
    public function __construct(
        private readonly EmpruntRepositoryInterface $emprunts,
        private readonly DocumentRepositoryInterface $documents,
        private readonly InscriptionRepositoryInterface $inscriptions,
        private readonly NotificateurBibliotheque $notificateur,
    ) {
    }

    private function regle(string $cle): int
    {
        return (int) config("acrest.bibliotheque.{$cle}");
    }

    /* ---------------------------------------------------------------
     | Actions de l'étudiant
     * ------------------------------------------------------------- */

    /** @throws BibliothequeException */
    public function demander(Inscription $lecteur, Document $document, ?string $message = null): Emprunt
    {
        $this->verifierLecteur($lecteur, $document);

        if (! $document->estEmpruntable()) {
            throw new BibliothequeException($document->estNumerique()
                ? 'Ce document est disponible en version numérique : consultez-le directement en ligne.'
                : 'Ce document est réservé à la consultation sur place et ne peut pas être emprunté.');
        }

        $emprunt = $this->emprunts->create([
            'inscription_id' => $lecteur->id,
            'document_id' => $document->id,
            'statut' => StatutEmprunt::Demande,
            'message' => $message,
        ]);

        $this->notificateur->etudiant($emprunt, EvenementEmprunt::Demande);
        $this->notificateur->bibliotheque($emprunt);

        return $emprunt;
    }

    /** @throws BibliothequeException */
    public function annuler(Emprunt $emprunt): Emprunt
    {
        if (! $emprunt->peutEtreAnnule()) {
            throw new BibliothequeException('Cette demande ne peut plus être annulée.');
        }

        DB::transaction(function () use ($emprunt) {
            $exemplaire = $emprunt->exemplaire;
            $this->emprunts->update($emprunt, ['statut' => StatutEmprunt::Annule, 'exemplaire_id' => null]);
            if ($exemplaire) {
                $this->liberer($exemplaire, $emprunt->traite_par);
            }
        });

        return $emprunt;
    }

    /** @throws BibliothequeException */
    public function prolonger(Emprunt $emprunt, ?int $agentId = null): Emprunt
    {
        if (! $emprunt->peutEtreProlonge()) {
            throw new BibliothequeException($emprunt->estEnRetard()
                ? 'Un document en retard ne peut pas être prolongé : merci de le rapporter.'
                : 'Ce prêt ne peut plus être prolongé.');
        }

        if ($this->emprunts->compterDemandes($emprunt->document) > 0) {
            throw new BibliothequeException('D\'autres étudiants attendent ce document : la prolongation n\'est pas possible.');
        }

        $this->emprunts->update($emprunt, [
            'date_retour_prevue' => $emprunt->date_retour_prevue->copy()->addDays($this->regle('duree_pret')),
            'prolongations' => $emprunt->prolongations + 1,
            'rappel_envoye_le' => null,
            'traite_par' => $agentId ?? $emprunt->traite_par,
        ]);

        $this->notificateur->etudiant($emprunt, EvenementEmprunt::Prolonge);

        return $emprunt;
    }

    /* ---------------------------------------------------------------
     | Actions du bibliothécaire
     * ------------------------------------------------------------- */

    /** Met un exemplaire de côté pour l'étudiant. @throws BibliothequeException */
    public function valider(Emprunt $emprunt, int $agentId): Emprunt
    {
        $this->exigerStatut($emprunt, StatutEmprunt::Demande);

        $reserve = DB::transaction(function () use ($emprunt, $agentId) {
            $exemplaire = $this->documents->exemplaireDisponible($emprunt->document);
            if (! $exemplaire) {
                return false;
            }
            $this->reserver($emprunt, $exemplaire, $agentId);

            return true;
        });

        if (! $reserve) {
            throw new BibliothequeException('Aucun exemplaire n\'est disponible pour le moment. La demande reste en file d\'attente et sera servie au prochain retour.');
        }

        $this->notificateur->etudiant($emprunt, EvenementEmprunt::Reserve);

        return $emprunt;
    }

    /** @throws BibliothequeException */
    public function refuser(Emprunt $emprunt, int $agentId, string $motif): Emprunt
    {
        if (! $emprunt->peutEtreAnnule()) {
            throw new BibliothequeException('Seule une demande en attente ou réservée peut être refusée.');
        }

        DB::transaction(function () use ($emprunt, $agentId, $motif) {
            $exemplaire = $emprunt->exemplaire;
            $this->emprunts->update($emprunt, ['statut' => StatutEmprunt::Refuse, 'motif' => $motif, 'traite_par' => $agentId, 'exemplaire_id' => null]);
            if ($exemplaire) {
                $this->liberer($exemplaire, $agentId);
            }
        });

        $this->notificateur->etudiant($emprunt, EvenementEmprunt::Refuse);

        return $emprunt;
    }

    /** Remise du document au guichet. @throws BibliothequeException */
    public function remettre(Emprunt $emprunt, int $agentId): Emprunt
    {
        if (! in_array($emprunt->statut, [StatutEmprunt::Demande, StatutEmprunt::Reserve], true)) {
            throw new BibliothequeException('Ce document a déjà été remis ou la demande est close.');
        }

        DB::transaction(function () use ($emprunt, $agentId) {
            $exemplaire = $emprunt->exemplaire ?? $this->documents->exemplaireDisponible($emprunt->document);
            if (! $exemplaire) {
                throw new BibliothequeException('Aucun exemplaire disponible à remettre.');
            }
            $this->preter($emprunt, $exemplaire, $agentId);
        });

        $this->notificateur->etudiant($emprunt, EvenementEmprunt::Remis);

        return $emprunt;
    }

    /** Prêt direct au guichet, à partir du code étudiant et du code-barres de l'exemplaire. @throws BibliothequeException */
    public function pretDirect(string $codeEtudiant, string $codeExemplaire, int $agentId): Emprunt
    {
        $lecteur = $this->inscriptions->findByCode($codeEtudiant)
            ?? throw new BibliothequeException('Aucun étudiant ne correspond à ce code d\'inscription.');
        $exemplaire = $this->documents->exemplaireParCode($codeExemplaire)
            ?? throw new BibliothequeException('Aucun exemplaire ne porte ce code-barres.');
        $document = $exemplaire->document;

        if ($document->consultation_sur_place) {
            throw new BibliothequeException('Ce document est réservé à la consultation sur place.');
        }

        // Si l'étudiant avait déjà une demande pour ce document, on la sert au lieu d'en créer une autre.
        $existante = Emprunt::where('inscription_id', $lecteur->id)->where('document_id', $document->id)
            ->whereIn('statut', [StatutEmprunt::Demande, StatutEmprunt::Reserve])->first();

        $emprunt = DB::transaction(function () use ($lecteur, $document, $exemplaire, $existante, $agentId) {
            $exemplaire = Exemplaire::lockForUpdate()->find($exemplaire->id);
            $reservePourLui = $existante && $existante->exemplaire_id === $exemplaire->id;

            if ($exemplaire->etat !== EtatExemplaire::Disponible && ! $reservePourLui) {
                throw new BibliothequeException("L'exemplaire {$exemplaire->code} n'est pas disponible ({$exemplaire->etat->libelle()}).");
            }

            if ($existante) {
                if ($existante->exemplaire && ! $reservePourLui) {
                    // Il repart avec un autre exemplaire : celui mis de côté est libéré.
                    $this->liberer($existante->exemplaire, $agentId);
                }
                $emprunt = $existante;
            } else {
                $this->verifierLecteur($lecteur, $document);
                $emprunt = $this->emprunts->create([
                    'inscription_id' => $lecteur->id,
                    'document_id' => $document->id,
                    'statut' => StatutEmprunt::Demande,
                ]);
            }

            return $this->preter($emprunt, $exemplaire, $agentId);
        });

        $this->notificateur->etudiant($emprunt, EvenementEmprunt::Remis);

        return $emprunt;
    }

    /** @throws BibliothequeException */
    public function retourner(Emprunt $emprunt, int $agentId, bool $horsService = false): Emprunt
    {
        $this->exigerStatut($emprunt, StatutEmprunt::EnCours);

        DB::transaction(function () use ($emprunt, $agentId, $horsService) {
            $this->emprunts->update($emprunt, ['statut' => StatutEmprunt::Rendu, 'date_retour' => now(), 'traite_par' => $agentId]);

            if ($horsService) {
                $emprunt->exemplaire?->update(['etat' => EtatExemplaire::Indisponible]);
            } elseif ($emprunt->exemplaire) {
                $this->liberer($emprunt->exemplaire, $agentId);
            }
        });

        $this->notificateur->etudiant($emprunt, EvenementEmprunt::Rendu);

        return $emprunt;
    }

    /* ---------------------------------------------------------------
     | Tâche quotidienne
     * ------------------------------------------------------------- */

    /**
     * Annule les réservations non retirées, envoie les rappels d'échéance et relance les retards.
     *
     * @return array{expirees:int, rappels:int, relances:int}
     */
    public function traiterEcheances(): array
    {
        $bilan = ['expirees' => 0, 'rappels' => 0, 'relances' => 0];

        foreach ($this->emprunts->reservationsExpirees() as $emprunt) {
            DB::transaction(function () use ($emprunt) {
                $exemplaire = $emprunt->exemplaire;
                $this->emprunts->update($emprunt, ['statut' => StatutEmprunt::Annule, 'motif' => 'Document non retiré dans le délai.', 'exemplaire_id' => null]);
                if ($exemplaire) {
                    $this->liberer($exemplaire, $emprunt->traite_par);
                }
            });
            $this->notificateur->etudiant($emprunt, EvenementEmprunt::Expire);
            $bilan['expirees']++;
        }

        foreach ($this->emprunts->aRappeler($this->regle('rappel_avant_echeance')) as $emprunt) {
            if ($this->notificateur->etudiant($emprunt, EvenementEmprunt::Rappel)) {
                $this->emprunts->update($emprunt, ['rappel_envoye_le' => now()]);
                $bilan['rappels']++;
            }
        }

        foreach ($this->emprunts->aRelancer($this->regle('relance_tous_les')) as $emprunt) {
            if ($this->notificateur->etudiant($emprunt, EvenementEmprunt::Retard)) {
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
    private function verifierLecteur(Inscription $lecteur, Document $document): void
    {
        if (! $lecteur->peutEmprunter()) {
            throw new BibliothequeException('Le prêt est réservé aux étudiants dont le dossier d\'inscription est validé.');
        }

        if ($this->emprunts->aDesRetards($lecteur)) {
            throw new BibliothequeException('Vous avez un document en retard : rapportez-le avant de faire une nouvelle demande.');
        }

        if ($this->emprunts->demandeActive($lecteur, $document)) {
            throw new BibliothequeException('Vous avez déjà une demande ou un emprunt en cours pour ce document.');
        }

        $max = $this->regle('max_emprunts');
        if ($this->emprunts->compterActifs($lecteur) >= $max) {
            throw new BibliothequeException("Vous avez atteint la limite de {$max} emprunts ou demandes simultanés.");
        }
    }

    /** @throws BibliothequeException */
    private function exigerStatut(Emprunt $emprunt, StatutEmprunt $attendu): void
    {
        if ($emprunt->statut !== $attendu) {
            throw new BibliothequeException("Opération impossible : cet emprunt est « {$emprunt->statut->libelle()} ».");
        }
    }

    private function reserver(Emprunt $emprunt, Exemplaire $exemplaire, ?int $agentId): void
    {
        $exemplaire->update(['etat' => EtatExemplaire::Reserve]);
        $this->emprunts->update($emprunt, [
            'statut' => StatutEmprunt::Reserve,
            'exemplaire_id' => $exemplaire->id,
            'retirer_avant' => today()->addDays($this->regle('delai_retrait')),
            'traite_par' => $agentId,
        ]);
    }

    private function preter(Emprunt $emprunt, Exemplaire $exemplaire, int $agentId): Emprunt
    {
        $exemplaire->update(['etat' => EtatExemplaire::Emprunte]);

        return $this->emprunts->update($emprunt, [
            'statut' => StatutEmprunt::EnCours,
            'exemplaire_id' => $exemplaire->id,
            'date_pret' => now(),
            'date_retour_prevue' => today()->addDays($this->regle('duree_pret')),
            'retirer_avant' => null,
            'traite_par' => $agentId,
        ]);
    }

    /** Remet l'exemplaire en rayon, ou le met de côté pour la prochaine demande en attente. */
    private function liberer(Exemplaire $exemplaire, ?int $agentId): void
    {
        $suivante = $this->emprunts->prochaineDemande($exemplaire->document);

        if (! $suivante) {
            $exemplaire->update(['etat' => EtatExemplaire::Disponible]);

            return;
        }

        $this->reserver($suivante, $exemplaire, $agentId);
        DB::afterCommit(fn () => $this->notificateur->etudiant($suivante, EvenementEmprunt::Reserve));
    }
}
