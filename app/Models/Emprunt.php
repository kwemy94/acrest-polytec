<?php

namespace App\Models;

use App\Enums\EtatPhysique;
use App\Enums\StatutEmprunt;
use App\Services\Bibliotheque\ParametresPret;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Emprunt extends Model
{
    protected $fillable = [
        'adherent_id',
        'document_id',
        'exemplaire_id',
        'exemplaire_actif_id',
        'statut',
        'canal',
        'message',
        'motif',
        'retirer_avant',
        'date_pret',
        'date_retour_prevue',
        'date_retour',
        'jours_retard',
        'etat_retour',
        'prolongations',
        'rappel_envoye_le',
        'derniere_relance_le',
        'traite_par',
    ];

    protected function casts(): array
    {
        return [
            'statut' => StatutEmprunt::class,
            'etat_retour' => EtatPhysique::class,
            'retirer_avant' => 'date',
            'date_pret' => 'datetime',
            'date_retour_prevue' => 'date',
            'date_retour' => 'datetime',
            'rappel_envoye_le' => 'datetime',
            'derniere_relance_le' => 'datetime',
            'prolongations' => 'integer',
            'jours_retard' => 'integer',
        ];
    }

    public function adherent(): BelongsTo
    {
        return $this->belongsTo(Adherent::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function exemplaire(): BelongsTo
    {
        return $this->belongsTo(Exemplaire::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'traite_par');
    }

    public function estEnRetard(): bool
    {
        return $this->statut === StatutEmprunt::EnCours && $this->date_retour_prevue?->isBefore(today());
    }

    /** Retard en jours : enregistré au retour, calculé à la date du jour pour un prêt en cours. */
    public function joursDeRetard(): int
    {
        if ($this->jours_retard !== null) {
            return $this->jours_retard;
        }

        return $this->date_retour_prevue && today()->isAfter($this->date_retour_prevue)
            ? (int) $this->date_retour_prevue->diffInDays(today())
            : 0;
    }

    public function peutEtreProlonge(): bool
    {
        return $this->statut === StatutEmprunt::EnCours
            && ! $this->estEnRetard()
            && $this->prolongations < app(ParametresPret::class)->maxProlongations();
    }

    public function peutEtreAnnule(): bool
    {
        return in_array($this->statut, [StatutEmprunt::Demande, StatutEmprunt::Reserve], true);
    }
}
