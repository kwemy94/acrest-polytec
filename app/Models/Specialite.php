<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Specialite extends Model
{
    protected $fillable = [
        'filiere_id',
        'nom',
        'slug',
        'resume',
        'sections',
        'diplome_requis',
        'images',
        'icone',
        'brochure',
        'ordre',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'sections' => 'array',
            'images' => 'array',
            'active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    public function scopeActives(Builder $query): Builder
    {
        return $query->where('active', true)->orderBy('ordre');
    }

    /** Image principale (ou celle de la filière à défaut). */
    public function couverture(): ?string
    {
        return $this->images[0] ?? $this->filiere?->image;
    }
}
