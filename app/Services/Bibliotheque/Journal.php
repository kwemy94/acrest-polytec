<?php

namespace App\Services\Bibliotheque;

use App\Models\Adherent;
use App\Models\JournalActivite;
use Illuminate\Database\Eloquent\Model;

/** Journal des opérations importantes (traçabilité). */
class Journal
{
    public const ACTIONS = [
        'document.creation' => 'Création d\'un document',
        'document.modification' => 'Modification d\'un document',
        'document.suppression' => 'Suppression d\'un document',
        'exemplaire.ajout' => 'Ajout d\'exemplaire',
        'exemplaire.modification' => 'Modification d\'exemplaire',
        'exemplaire.localisation' => 'Changement de localisation',
        'exemplaire.statut' => 'Changement de statut',
        'exemplaire.suppression' => 'Suppression d\'exemplaire',
        'pret' => 'Prêt',
        'retour' => 'Retour',
        'perte' => 'Perte',
        'prolongation' => 'Prolongation',
        'demande' => 'Demande en ligne',
        'demande.traitement' => 'Traitement d\'une demande',
        'ressource.ajout' => 'Ajout de ressource numérique',
        'ressource.modification' => 'Modification de ressource numérique',
        'ressource.suppression' => 'Suppression de ressource numérique',
        'adherent.creation' => 'Création d\'adhérent',
        'adherent.modification' => 'Modification d\'adhérent',
        'localisation' => 'Gestion des espaces',
        'administration' => 'Administration',
    ];

    public function enregistrer(string $action, ?Model $sujet, string $description, array $proprietes = [], ?Adherent $adherent = null): JournalActivite
    {
        return JournalActivite::create([
            'user_id' => auth()->id(),
            'adherent_id' => $adherent?->id,
            'action' => $action,
            'sujet_type' => $sujet?->getMorphClass(),
            'sujet_id' => $sujet?->getKey(),
            'description' => mb_substr($description, 0, 500),
            'proprietes' => $proprietes ?: null,
            'ip' => request()?->ip(),
        ]);
    }
}
