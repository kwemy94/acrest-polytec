<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Filiere extends Model
{
    protected $fillable = [
        'nom',
        'slug',
        'domaine',
        'description',
        'image',
        'ordre',
        'active',
    ];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function specialites(): HasMany
    {
        return $this->hasMany(Specialite::class)->orderBy('ordre');
    }

    public function scopeActives(Builder $query): Builder
    {
        return $query->where('active', true)->orderBy('ordre');
    }
}
