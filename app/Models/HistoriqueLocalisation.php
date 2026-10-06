<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriqueLocalisation extends Model
{
    protected $fillable = ['exemplaire_id', 'ancienne_localisation_id', 'nouvelle_localisation_id', 'user_id', 'motif'];

    public function exemplaire(): BelongsTo
    {
        return $this->belongsTo(Exemplaire::class);
    }

    public function ancienne(): BelongsTo
    {
        return $this->belongsTo(Localisation::class, 'ancienne_localisation_id');
    }

    public function nouvelle(): BelongsTo
    {
        return $this->belongsTo(Localisation::class, 'nouvelle_localisation_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
