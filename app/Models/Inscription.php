<?php

namespace App\Models;

use App\Enums\Sexe;
use App\Enums\StatutInscription;
use App\Enums\StatutPaiement;
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

    public function emprunts(): HasMany
    {
        return $this->hasMany(Emprunt::class)->latest();
    }

    /** Seuls les étudiants dont le dossier est validé ont accès au prêt. */
    public function peutEmprunter(): bool
    {
        return $this->statut === StatutInscription::Validee;
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
