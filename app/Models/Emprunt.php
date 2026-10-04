<?php

namespace App\Models;

use App\Enums\StatutEmprunt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Emprunt extends Model
{
    protected $fillable = [
        'inscription_id',
        'document_id',
        'exemplaire_id',
        'statut',
        'message',
        'motif',
        'retirer_avant',
        'date_pret',
        'date_retour_prevue',
        'date_retour',
        'prolongations',
        'rappel_envoye_le',
        'derniere_relance_le',
        'traite_par',
    ];

    protected function casts(): array
    {
        return [
            'statut' => StatutEmprunt::class,
            'retirer_avant' => 'date',
            'date_pret' => 'datetime',
            'date_retour_prevue' => 'date',
            'date_retour' => 'datetime',
            'rappel_envoye_le' => 'datetime',
            'derniere_relance_le' => 'datetime',
            'prolongations' => 'integer',
        ];
    }

    public function inscription(): BelongsTo
    {
        return $this->belongsTo(Inscription::class)->withTrashed();
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

    public function joursDeRetard(): int
    {
        $fin = $this->date_retour ? $this->date_retour->copy()->startOfDay() : today();

        return $this->date_retour_prevue && $fin->isAfter($this->date_retour_prevue)
            ? (int) $this->date_retour_prevue->diffInDays($fin)
            : 0;
    }

    public function peutEtreProlonge(): bool
    {
        return $this->statut === StatutEmprunt::EnCours
            && ! $this->estEnRetard()
            && $this->prolongations < (int) config('acrest.bibliotheque.max_prolongations');
    }

    public function peutEtreAnnule(): bool
    {
        return in_array($this->statut, [StatutEmprunt::Demande, StatutEmprunt::Reserve], true);
    }
}
