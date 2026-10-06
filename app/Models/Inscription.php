<?php

namespace App\Models;

use App\Enums\Sexe;
use App\Enums\StatutInscription;
use App\Enums\StatutPaiement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'nom',
        'prenom',
        'sexe',
        'date_naissance',
        'lieu_naissance',
        'pays',
        'cni',
        'telephone',
        'email',
        'nom_pere',
        'nom_mere',
        'contact_parent',
        'diplome',
        'option_diplome',
        'annee_obtention',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
            'sexe' => Sexe::class,
            'statut' => StatutInscription::class,
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    /** Spécialités choisies, triées par ordre de préférence. */
    public function specialites(): BelongsToMany
    {
        return $this->belongsToMany(Specialite::class, 'inscription_choix')
            ->withPivot('rang')
            ->withTimestamps()
            ->orderByPivot('rang');
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class)->latest();
    }

    /** Étudiants en règle : dossier validé et frais d'inscription payés (paiement confirmé). */
    public function scopeEnRegle(Builder $query): Builder
    {
        return $query->where('statut', StatutInscription::Validee->value)
            ->whereHas('paiements', fn (Builder $p) => $p->where('statut', StatutPaiement::Valide->value));
    }

    /** Étudiants en règle qui n'ont pas encore de fiche adhérent à la bibliothèque. */
    public function scopeSansFicheAdherent(Builder $query): Builder
    {
        return $query->whereDoesntHave('adherent');
    }

    /** Fiche adhérent de la bibliothèque créée à partir de ce dossier. */
    public function adherent(): HasOne
    {
        return $this->hasOne(Adherent::class);
    }

    public function dernierPaiement(): HasOne
    {
        return $this->hasOne(Paiement::class)->latestOfMany();
    }

    protected function nomComplet(): Attribute
    {
        return Attribute::get(fn () => trim($this->nom.' '.$this->prenom));
    }

    public function estPayee(): bool
    {
        return $this->paiements->contains(fn (Paiement $p) => $p->statut === StatutPaiement::Valide);
    }

    public function aPaiementEnCours(): bool
    {
        return $this->paiements->contains(fn (Paiement $p) => $p->statut === StatutPaiement::EnAttente);
    }
}
