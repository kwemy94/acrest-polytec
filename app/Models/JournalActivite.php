<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/** Trace d'une opération : qui, quoi, quand, sur quel élément. */
class JournalActivite extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'adherent_id', 'action', 'sujet_type', 'sujet_id', 'description', 'proprietes', 'ip'];

    protected function casts(): array
    {
        return ['proprietes' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function adherent(): BelongsTo
    {
        return $this->belongsTo(Adherent::class);
    }

    public function sujet(): MorphTo
    {
        return $this->morphTo();
    }
}
