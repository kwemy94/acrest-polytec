<?php

namespace App\Repositories\Eloquent;

use App\Enums\StatutAdherent;
use App\Enums\StatutEmprunt;
use App\Models\Adherent;
use App\Repositories\Contracts\AdherentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class AdherentRepository extends BaseRepository implements AdherentRepositoryInterface
{
    public function __construct(Adherent $model)
    {
        parent::__construct($model);
    }

    private function actifs(Builder $q): Builder
    {
        return $q->where('statut', StatutAdherent::Actif->value)
            ->where(fn (Builder $w) => $w->whereNull('date_expiration')->orWhereDate('date_expiration', '>=', today()));
    }

    public function rechercher(array $filtres, int $perPage = 25): LengthAwarePaginator
    {
        $statut = $filtres['statut'] ?? null;

        return $this->query()
            ->withCount(['emprunts as prets_en_cours_count' => fn ($q) => $q->where('statut', StatutEmprunt::EnCours->value)])
            ->when($filtres['q'] ?? null, function (Builder $q, string $terme) {
                $q->where(fn (Builder $w) => $w
                    ->where('matricule', 'like', "%{$terme}%")
                    ->orWhere('nom', 'like', "%{$terme}%")
                    ->orWhere('prenom', 'like', "%{$terme}%")
                    ->orWhere('email', 'like', "%{$terme}%")
                    ->orWhere('telephone', 'like', "%{$terme}%"));
            })
            ->when($filtres['type'] ?? null, fn (Builder $q, string $t) => $q->where('type', $t))
            ->when($statut === StatutAdherent::Actif->value, fn (Builder $q) => $this->actifs($q))
            ->when($statut === StatutAdherent::Expire->value, fn (Builder $q) => $q->where(fn (Builder $w) => $w
                ->where('statut', StatutAdherent::Expire->value)
                ->orWhereDate('date_expiration', '<', today())))
            ->when($statut === StatutAdherent::Suspendu->value, fn (Builder $q) => $q->where('statut', $statut))
            ->orderBy('nom')
            ->orderBy('prenom')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function parMatricule(string $matricule): ?Adherent
    {
        return $this->query()->where('matricule', strtoupper(trim($matricule)))->first();
    }

    public function compterActifs(): int
    {
        return $this->actifs($this->query())->count();
    }

    public function expirerEchus(): int
    {
        return $this->query()
            ->where('statut', StatutAdherent::Actif->value)
            ->whereDate('date_expiration', '<', today())
            ->update(['statut' => StatutAdherent::Expire->value, 'updated_at' => now()]);
    }
}
