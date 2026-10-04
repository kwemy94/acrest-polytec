<?php

namespace App\Repositories\Eloquent;

use App\Enums\StatutPaiement;
use App\Models\Paiement;
use App\Repositories\Contracts\PaiementRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class PaiementRepository extends BaseRepository implements PaiementRepositoryInterface
{
    public function __construct(Paiement $model)
    {
        parent::__construct($model);
    }

    public function referenceExiste(string $reference): bool
    {
        return $this->query()->where('reference', $reference)->exists();
    }

    public function rechercher(array $filtres, int $perPage = 20): LengthAwarePaginator
    {
        return $this->query()
            ->with(['inscription', 'agent'])
            ->when($filtres['q'] ?? null, function (Builder $q, string $terme) {
                $q->where(fn (Builder $w) => $w
                    ->where('reference', 'like', "%{$terme}%")
                    ->orWhere('telephone', 'like', "%{$terme}%")
                    ->orWhereHas('inscription', fn (Builder $i) => $i
                        ->where('code', 'like', "%{$terme}%")
                        ->orWhere('nom', 'like', "%{$terme}%")
                        ->orWhere('prenom', 'like', "%{$terme}%")));
            })
            ->when($filtres['statut'] ?? null, fn (Builder $q, string $s) => $q->where('statut', $s))
            ->when($filtres['operateur'] ?? null, fn (Builder $q, string $o) => $q->where('operateur', $o))
            ->orderByRaw('case when statut = ? then 0 else 1 end', [StatutPaiement::EnAttente->value])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function changerStatut(Paiement $paiement, StatutPaiement $statut, int $agentId, ?string $note = null): Paiement
    {
        $paiement->update([
            'statut' => $statut,
            'note' => $note,
            'traite_le' => now(),
            'traite_par' => $agentId,
        ]);

        return $paiement;
    }

    public function totalEncaisse(): int
    {
        return (int) $this->query()->where('statut', StatutPaiement::Valide->value)->sum('montant');
    }

    public function compterEnAttente(): int
    {
        return $this->query()->where('statut', StatutPaiement::EnAttente->value)->count();
    }
}
