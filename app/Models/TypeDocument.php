<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Type de document (livre, mémoire, thèse…) : liste configurable par l'administrateur. */
class TypeDocument extends Model
{
    protected $table = 'types_documents';

    protected $fillable = ['nom', 'ordre', 'actif'];

    protected function casts(): array
    {
        return ['actif' => 'boolean', 'ordre' => 'integer'];
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function scopeActifs(Builder $query): Builder
    {
        return $query->where('actif', true)->orderBy('ordre')->orderBy('nom');
    }
}
