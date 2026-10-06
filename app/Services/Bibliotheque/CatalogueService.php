<?php

namespace App\Services\Bibliotheque;

use App\Enums\StatutExemplaire;
use App\Exceptions\BibliothequeException;
use App\Models\Document;
use App\Models\Exemplaire;
use App\Models\HistoriqueLocalisation;
use App\Models\Incident;
use App\Enums\TypeIncident;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use App\Repositories\Contracts\ExemplaireRepositoryInterface;
use Illuminate\Support\Facades\DB;

/** Documents et exemplaires physiques, avec traçabilité de chaque opération. */
class CatalogueService
{
    public function __construct(
        private readonly DocumentRepositoryInterface $documents,
        private readonly ExemplaireRepositoryInterface $exemplaires,
        private readonly BibliothequeNumerique $numerique,
        private readonly PlanBibliotheque $plan,
        private readonly Journal $journal,
    ) {
    }

    /* ---------------------------------------------------------------
     | Documents
     * ------------------------------------------------------------- */

    /** @param list<string> $auteurs */
    public function creerDocument(array $attributs, array $auteurs): Document
    {
        $document = $this->documents->enregistrer(new Document(['cree_par' => auth()->id()]), $attributs, $auteurs);
        $this->journal->enregistrer('document.creation', $document, "Création du document « {$document->titre} »");

        return $document;
    }

    /** @param list<string> $auteurs */
    public function modifierDocument(Document $document, array $attributs, array $auteurs): Document
    {
        $avant = $document->only(array_keys($attributs));
        $auteursAvant = $document->auteurs()->pluck('nom')->all();

        $this->documents->enregistrer($document, $attributs, $auteurs);

        $changes = collect($document->getChanges())->except(['updated_at'])->keys()->all();
        if ($auteursAvant !== $document->auteurs->pluck('nom')->all()) {
            $changes[] = 'auteurs';
        }

        $this->journal->enregistrer('document.modification', $document, "Modification du document « {$document->titre} »", [
            'champs' => $changes,
            'avant' => collect($avant)->only($changes)->all(),
        ]);

        return $document;
    }

    /** @throws BibliothequeException */
    public function supprimerDocument(Document $document): void
    {
        if ($document->emprunts()->exists()) {
            throw new BibliothequeException('Ce document a un historique de prêts : retirez plutôt ses exemplaires du catalogue.');
        }

        DB::transaction(function () use ($document) {
            foreach ($document->ressources as $ressource) {
                $this->numerique->supprimer($ressource);
            }
            $this->journal->enregistrer('document.suppression', $document, "Suppression du document « {$document->titre} »");
            $this->documents->delete($document);
        });
    }

    /* ---------------------------------------------------------------
     | Exemplaires
     * ------------------------------------------------------------- */

    /**
     * Ajoute N exemplaires avec un code d'inventaire unique chacun.
     *
     * @param  array{localisation_id?:?int, date_acquisition?:?string, source_acquisition?:?string, etat_physique?:string, code_barres?:?string}  $attributs
     * @return list<Exemplaire>
     */
    public function ajouterExemplaires(Document $document, int $nombre, array $attributs = []): array
    {
        return DB::transaction(function () use ($document, $nombre, $attributs) {
            $crees = [];

            for ($i = 0; $i < $nombre; $i++) {
                $exemplaire = $document->exemplaires()->create([
                    ...$attributs,
                    // Un code-barres saisi n'a de sens que pour un seul exemplaire.
                    'code_barres' => $nombre === 1 ? ($attributs['code_barres'] ?? null) : null,
                    'code_inventaire' => $this->exemplaires->prochainCodeInventaire(),
                    'statut' => StatutExemplaire::Disponible,
                ]);

                if ($exemplaire->localisation_id) {
                    HistoriqueLocalisation::create([
                        'exemplaire_id' => $exemplaire->id,
                        'nouvelle_localisation_id' => $exemplaire->localisation_id,
                        'user_id' => auth()->id(),
                        'motif' => 'Mise en rayon initiale',
                    ]);
                }

                $this->journal->enregistrer('exemplaire.ajout', $exemplaire,
                    "Ajout de l'exemplaire {$exemplaire->code_inventaire} à « {$document->titre} »",
                    ['localisation' => $this->plan->chemin($exemplaire->localisation_id) ?: null]);
                $crees[] = $exemplaire;
            }

            return $crees;
        });
    }

    public function modifierExemplaire(Exemplaire $exemplaire, array $attributs): Exemplaire
    {
        $exemplaire->update($attributs);
        $changes = collect($exemplaire->getChanges())->except('updated_at')->keys()->all();

        if ($changes) {
            $this->journal->enregistrer('exemplaire.modification', $exemplaire, "Modification de l'exemplaire {$exemplaire->code_inventaire}", ['champs' => $changes]);
        }

        return $exemplaire;
    }

    /** Change la localisation et conserve l'historique du déplacement. */
    public function deplacer(Exemplaire $exemplaire, ?int $localisationId, ?string $motif = null): Exemplaire
    {
        if ((int) $exemplaire->localisation_id === (int) $localisationId) {
            return $exemplaire;
        }

        DB::transaction(function () use ($exemplaire, $localisationId, $motif) {
            $ancienne = $exemplaire->localisation_id;
            $exemplaire->update(['localisation_id' => $localisationId]);

            HistoriqueLocalisation::create([
                'exemplaire_id' => $exemplaire->id,
                'ancienne_localisation_id' => $ancienne,
                'nouvelle_localisation_id' => $localisationId,
                'user_id' => auth()->id(),
                'motif' => $motif,
            ]);

            $de = $this->plan->chemin($ancienne) ?: '—';
            $vers = $this->plan->chemin($localisationId) ?: '—';
            $this->journal->enregistrer('exemplaire.localisation', $exemplaire,
                "Déplacement de {$exemplaire->code_inventaire} : {$de} ⇒ {$vers}", array_filter(['motif' => $motif]));
        });

        return $exemplaire->load('localisation');
    }

    /**
     * Statut manuel : remise en rayon, perte, réparation, retrait.
     *
     * @throws BibliothequeException
     */
    public function changerStatut(Exemplaire $exemplaire, StatutExemplaire $statut, ?string $motif = null): Exemplaire
    {
        if (! in_array($statut, StatutExemplaire::manuels(), true)) {
            throw new BibliothequeException('Ce statut est attribué automatiquement par les prêts et réservations.');
        }

        if ($exemplaire->statut->enCirculation()) {
            throw new BibliothequeException("L'exemplaire {$exemplaire->code_inventaire} est {$exemplaire->statut->libelle()} : enregistrez d'abord son retour ou annulez la réservation.");
        }

        if ($exemplaire->statut === $statut) {
            return $exemplaire;
        }

        DB::transaction(function () use ($exemplaire, $statut, $motif) {
            $ancien = $exemplaire->statut;
            $exemplaire->update(['statut' => $statut]);

            if ($statut === StatutExemplaire::Perdu) {
                Incident::create([
                    'exemplaire_id' => $exemplaire->id,
                    'type' => TypeIncident::Perte,
                    'description' => $motif,
                    'user_id' => auth()->id(),
                ]);
            }

            $this->journal->enregistrer($statut === StatutExemplaire::Perdu ? 'perte' : 'exemplaire.statut', $exemplaire,
                "Exemplaire {$exemplaire->code_inventaire} : {$ancien->libelle()} ⇒ {$statut->libelle()}",
                array_filter(['motif' => $motif]));
        });

        return $exemplaire;
    }

    /** @throws BibliothequeException */
    public function supprimerExemplaire(Exemplaire $exemplaire): void
    {
        if ($exemplaire->emprunts()->exists()) {
            throw new BibliothequeException('Cet exemplaire a un historique de prêts : passez-le plutôt au statut « Retiré du catalogue ».');
        }

        $this->journal->enregistrer('exemplaire.suppression', $exemplaire->document,
            "Suppression de l'exemplaire {$exemplaire->code_inventaire}", ['code_inventaire' => $exemplaire->code_inventaire]);
        $exemplaire->delete();
    }
}
