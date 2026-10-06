<?php

namespace App\Services\Bibliotheque;

use App\Enums\StatutAdherent;
use App\Enums\TypeAdherent;
use App\Models\Adherent;
use App\Models\Inscription;
use Illuminate\Support\Facades\DB;

class AdherentService
{
    public function __construct(private readonly Journal $journal)
    {
    }

    public function creer(array $attributs): Adherent
    {
        $adherent = Adherent::create($attributs);
        $this->journal->enregistrer('adherent.creation', $adherent, "Inscription de l'adhérent {$adherent->nom_complet} ({$adherent->matricule})");

        return $adherent;
    }

    public function modifier(Adherent $adherent, array $attributs): Adherent
    {
        $statutAvant = $adherent->statut;
        $adherent->update($attributs);
        $changes = collect($adherent->getChanges())->except('updated_at')->keys()->all();

        if ($changes) {
            $description = $adherent->statut !== $statutAvant
                ? "Adhérent {$adherent->matricule} : {$statutAvant->libelle()} ⇒ {$adherent->statut->libelle()}"
                : "Modification de l'adhérent {$adherent->matricule}";
            $this->journal->enregistrer('adherent.modification', $adherent, $description, ['champs' => $changes]);
        }

        return $adherent;
    }

    /**
     * Crée une fiche adhérent pour chaque étudiant en règle (dossier validé, frais payés) qui n'en a pas encore.
     * Le matricule est le code d'inscription.
     */
    public function importerEtudiants(?string $dateExpiration = null): int
    {
        $inscriptions = Inscription::enRegle()->sansFicheAdherent()->get();

        DB::transaction(function () use ($inscriptions, $dateExpiration) {
            foreach ($inscriptions as $inscription) {
                if (Adherent::where('matricule', $inscription->code)->exists()) {
                    continue;
                }
                Adherent::create([
                    'matricule' => $inscription->code,
                    'nom' => $inscription->nom,
                    'prenom' => $inscription->prenom,
                    'telephone' => $inscription->telephone,
                    'email' => $inscription->email,
                    'type' => TypeAdherent::Etudiant,
                    'date_inscription' => today(),
                    'date_expiration' => $dateExpiration ?? today()->addYear(),
                    'statut' => StatutAdherent::Actif,
                    'inscription_id' => $inscription->id,
                ]);
            }
        });

        if ($inscriptions->isNotEmpty()) {
            $this->journal->enregistrer('adherent.creation', null, "Import de {$inscriptions->count()} étudiant(s) depuis les inscriptions validées");
        }

        return $inscriptions->count();
    }
}
