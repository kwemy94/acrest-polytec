<?php

namespace App\Models;

use App\Enums\EtatExemplaire;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exemplaire extends Model
{
    protected $fillable = [
        'document_id',
        'code',
        'etat',
    ];

    protected function casts(): array
    {
        return ['etat' => EtatExemplaire::class];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function emprunts(): HasMany
    {
        return $this->hasMany(Emprunt::class);
    }
}
