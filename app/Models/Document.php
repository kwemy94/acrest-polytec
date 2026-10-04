<?php

namespace App\Models;

use App\Enums\EtatExemplaire;
use App\Enums\TypeDocument;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Notice bibliographique. Les compteurs `disponibles_count` et `en_circulation_count`
 * sont chargés par le repository (scope avecDisponibilite).
 */
class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'auteurs',
        'editeur',
        'annee_publication',
        'isbn',
        'cote',
        'type',
        'langue',
        'filiere_id',
        'resume',
        'consultation_sur_place',
        'fichier',
        'fichier_taille',
        'telechargeable',
    ];

    protected $attributes = [
        'langue' => 'fr',
    ];

    protected function casts(): array
    {
        return [
            'type' => TypeDocument::class,
            'consultation_sur_place' => 'boolean',
            'annee_publication' => 'integer',
            'fichier_taille' => 'integer',
            'telechargeable' => 'boolean',
        ];
    }

    protected function langueLibelle(): Attribute
    {
        return Attribute::get(fn () => config("acrest.langues.{$this->langue}") ?? strtoupper((string) $this->langue));
    }

    /** Une version PDF est consultable en ligne. */
    public function estNumerique(): bool
    {
        return (bool) $this->fichier;
    }

    /** Taille du PDF lisible (« 3,2 Mo »). */
    protected function tailleFichier(): Attribute
    {
        return Attribute::get(fn () => $this->fichier_taille
            ? number_format($this->fichier_taille / 1048576, 1, ',', ' ').' Mo'
            : null);
    }

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    public function exemplaires(): HasMany
    {
        return $this->hasMany(Exemplaire::class)->orderBy('id');
    }

    public function emprunts(): HasMany
    {
        return $this->hasMany(Emprunt::class);
    }

    public function scopeAvecDisponibilite($query)
    {
        return $query->withCount([
            'exemplaires as disponibles_count' => fn ($q) => $q->where('etat', EtatExemplaire::Disponible->value),
            'exemplaires as en_circulation_count' => fn ($q) => $q->where('etat', '!=', EtatExemplaire::Indisponible->value),
        ]);
    }

    /**
     * Peut faire l'objet d'une demande en ligne : prêt autorisé, pas de version numérique
     * (elle se consulte directement) et au moins un exemplaire en circulation.
     */
    public function estEmpruntable(): bool
    {
        if ($this->estNumerique()) {
            return false;
        }

        $enCirculation = $this->en_circulation_count
            ?? $this->exemplaires()->where('etat', '!=', EtatExemplaire::Indisponible->value)->count();

        return ! $this->consultation_sur_place && $enCirculation > 0;
    }
}
