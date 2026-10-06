<?php

namespace App\Models;

use App\Enums\NiveauAcces;
use App\Enums\StatutExemplaire;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Notice bibliographique : décrit l'œuvre.
 * Les compteurs `exemplaires_count`, `disponibles_count`, `empruntes_count` et `numeriques_count`
 * sont chargés par le scope avecDisponibilite().
 */
class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'sous_titre',
        'type_document_id',
        'categorie_id',
        'editeur',
        'annee_publication',
        'isbn',
        'langue',
        'description',
        'mots_cles',
        'nombre_pages',
        'cote',
        'consultation_sur_place',
        'cree_par',
    ];

    protected $attributes = [
        'langue' => 'fr',
    ];

    protected function casts(): array
    {
        return [
            'consultation_sur_place' => 'boolean',
            'annee_publication' => 'integer',
            'nombre_pages' => 'integer',
        ];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(TypeDocument::class, 'type_document_id');
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    public function auteurs(): BelongsToMany
    {
        return $this->belongsToMany(Auteur::class, 'document_auteur')->withPivot('ordre')->orderByPivot('ordre');
    }

    public function exemplaires(): HasMany
    {
        return $this->hasMany(Exemplaire::class)->orderBy('code_inventaire');
    }

    public function ressources(): HasMany
    {
        return $this->hasMany(RessourceNumerique::class)->latest();
    }

    public function emprunts(): HasMany
    {
        return $this->hasMany(Emprunt::class);
    }

    public function scopeAvecDisponibilite(Builder $query): Builder
    {
        $statut = fn (StatutExemplaire ...$s) => fn ($q) => $q->whereIn('statut', array_map(fn ($x) => $x->value, $s));

        return $query->withCount([
            'exemplaires' => $statut(...StatutExemplaire::fondsActif()),
            'exemplaires as disponibles_count' => $statut(StatutExemplaire::Disponible),
            'exemplaires as empruntes_count' => $statut(StatutExemplaire::Emprunte),
            'ressources as numeriques_count' => fn ($q) => $q->where('niveau_acces', '!=', NiveauAcces::Restreint->value),
        ]);
    }

    protected function nomsAuteurs(): Attribute
    {
        return Attribute::get(fn () => $this->auteurs->pluck('nom')->join(', '));
    }

    protected function titreComplet(): Attribute
    {
        return Attribute::get(fn () => $this->titre.($this->sous_titre ? ' : '.$this->sous_titre : ''));
    }

    protected function langueLibelle(): Attribute
    {
        return Attribute::get(fn () => config("acrest.langues.{$this->langue}") ?? strtoupper((string) $this->langue));
    }

    /** @return list<string> */
    public function listeMotsCles(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->mots_cles))));
    }
}
