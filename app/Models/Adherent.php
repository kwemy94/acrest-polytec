<?php

namespace App\Models;

use App\Enums\StatutAdherent;
use App\Enums\TypeAdherent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Personne autorisée à emprunter. */
class Adherent extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'telephone',
        'email',
        'type',
        'date_inscription',
        'date_expiration',
        'statut',
        'inscription_id',
        'notes',
    ];

    protected $attributes = [
        'statut' => 'actif',
    ];

    protected function casts(): array
    {
        return [
            'type' => TypeAdherent::class,
            'statut' => StatutAdherent::class,
            'date_inscription' => 'date',
            'date_expiration' => 'date',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'matricule';
    }

    public function emprunts(): HasMany
    {
        return $this->hasMany(Emprunt::class)->latest();
    }

    public function inscription(): BelongsTo
    {
        return $this->belongsTo(Inscription::class)->withTrashed();
    }

    protected function nomComplet(): Attribute
    {
        return Attribute::get(fn () => trim($this->nom.' '.$this->prenom));
    }

    /** Statut réel : un adhérent dont la date d'expiration est passée est expiré. */
    public function statutEffectif(): StatutAdherent
    {
        if ($this->statut === StatutAdherent::Actif && $this->date_expiration?->isBefore(today())) {
            return StatutAdherent::Expire;
        }

        return $this->statut;
    }

    public function estActif(): bool
    {
        return $this->statutEffectif() === StatutAdherent::Actif;
    }
}
