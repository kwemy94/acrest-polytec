<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Auteur extends Model
{
    protected $fillable = ['nom'];

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'document_auteur')->withPivot('ordre');
    }
}
