<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Catégorie / domaine du catalogue. */
class Categorie extends Model
{
    protected $fillable = ['nom', 'description'];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
