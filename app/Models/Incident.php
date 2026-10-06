<?php

namespace App\Models;

use App\Enums\TypeIncident;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Événement notable sur un exemplaire : perte, dommage, retour en retard… */
class Incident extends Model
{
    protected $fillable = ['exemplaire_id', 'emprunt_id', 'adherent_id', 'type', 'description', 'user_id'];

    protected function casts(): array
    {
        return ['type' => TypeIncident::class];
    }

    public function exemplaire(): BelongsTo
    {
        return $this->belongsTo(Exemplaire::class);
    }

    public function emprunt(): BelongsTo
    {
        return $this->belongsTo(Emprunt::class);
    }

    public function adherent(): BelongsTo
    {
        return $this->belongsTo(Adherent::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
