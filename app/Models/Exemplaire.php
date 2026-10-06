<?php

namespace App\Models;

use App\Enums\EtatPhysique;
use App\Enums\StatutExemplaire;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/** Copie physique d'un document, identifiée par son code d'inventaire. */
class Exemplaire extends Model
{
    protected $fillable = [
        'document_id',
        'code_inventaire',
        'code_barres',
        'date_acquisition',
        'source_acquisition',
        'etat_physique',
        'statut',
        'localisation_id',
        'notes',
    ];

    protected $attributes = [
        'etat_physique' => 'bon',
        'statut' => 'disponible',
    ];

    protected function casts(): array
    {
        return [
            'etat_physique' => EtatPhysique::class,
            'statut' => StatutExemplaire::class,
            'date_acquisition' => 'date',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'code_inventaire';
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function localisation(): BelongsTo
    {
        return $this->belongsTo(Localisation::class);
    }

    public function emprunts(): HasMany
    {
        return $this->hasMany(Emprunt::class)->latest();
    }

    /** Prêt en cours (au plus un, garanti par l'index unique exemplaire_actif_id). */
    public function empruntActif(): HasOne
    {
        return $this->hasOne(Emprunt::class, 'exemplaire_actif_id');
    }

    public function historiqueLocalisations(): HasMany
    {
        return $this->hasMany(HistoriqueLocalisation::class)->latest('id');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class)->latest('id');
    }

    public function estPretable(): bool
    {
        return $this->statut === StatutExemplaire::Disponible;
    }
}
