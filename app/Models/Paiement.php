<?php

namespace App\Models;

use App\Enums\OperateurPaiement;
use App\Enums\StatutPaiement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paiement extends Model
{
    protected $fillable = [
        'inscription_id',
        'operateur',
        'montant',
        'telephone',
        'reference',
        'statut',
        'note',
        'traite_le',
        'traite_par',
    ];

    protected function casts(): array
    {
        return [
            'operateur' => OperateurPaiement::class,
            'statut' => StatutPaiement::class,
            'traite_le' => 'datetime',
            'montant' => 'integer',
        ];
    }

    public function inscription(): BelongsTo
    {
        return $this->belongsTo(Inscription::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'traite_par');
    }
}
