<?php

namespace App\Models;

use App\Enums\TypeLocalisation;
use App\Services\Bibliotheque\PlanBibliotheque;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Espace physique de la bibliothèque, rattaché éventuellement à un espace parent. */
class Localisation extends Model
{
    protected $fillable = ['parent_id', 'nom', 'type', 'code', 'description'];

    protected function casts(): array
    {
        return ['type' => TypeLocalisation::class];
    }

    protected static function booted(): void
    {
        $oublier = fn () => app(PlanBibliotheque::class)->oublier();
        static::saved($oublier);
        static::deleted($oublier);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function enfants(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('nom');
    }

    public function exemplaires(): HasMany
    {
        return $this->hasMany(Exemplaire::class);
    }

    /** « Salle Sciences → Rayon Informatique → Étagère A ». */
    protected function chemin(): Attribute
    {
        return Attribute::get(fn () => app(PlanBibliotheque::class)->chemin($this->id));
    }
}
