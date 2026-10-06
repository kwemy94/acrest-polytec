<?php

namespace App\Repositories\Eloquent;

use App\Enums\StatutExemplaire;
use App\Models\Document;
use App\Models\Exemplaire;
use App\Repositories\Contracts\ExemplaireRepositoryInterface;
use App\Services\Bibliotheque\PlanBibliotheque;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ExemplaireRepository extends BaseRepository implements ExemplaireRepositoryInterface
{
    public function __construct(Exemplaire $model, private readonly PlanBibliotheque $plan)
    {
        parent::__construct($model);
    }

    public function rechercher(array $filtres, int $perPage = 25): LengthAwarePaginator
    {
        return $this->query()
            ->with(['document.auteurs', 'localisation', 'empruntActif.adherent'])
            ->when($filtres['q'] ?? null, function (Builder $q, string $terme) {
                $q->where(fn (Builder $w) => $w
                    ->where('code_inventaire', 'like', "%{$terme}%")
                    ->orWhere('code_barres', $terme)
                    ->orWhereHas('document', fn (Builder $d) => $d->where('titre', 'like', "%{$terme}%")));
            })
            ->when($filtres['statut'] ?? null, fn (Builder $q, string $s) => $q->where('statut', $s))
            ->when($filtres['etat'] ?? null, fn (Builder $q, string $e) => $q->where('etat_physique', $e))
            ->when($filtres['localisation'] ?? null, fn (Builder $q, $l) => $l === 'aucune'
                ? $q->whereNull('localisation_id')
                : $q->whereIn('localisation_id', $this->plan->sousArbre((int) $l)))
            ->orderBy('code_inventaire')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function parCode(string $code): ?Exemplaire
    {
        $code = strtoupper(trim($code));

        return $this->query()->with('document')
            ->where('code_inventaire', $code)
            ->orWhere('code_barres', $code)
            ->first();
    }

    public function disponiblePour(Document $document): ?Exemplaire
    {
        return $this->query()
            ->where('document_id', $document->id)
            ->where('statut', StatutExemplaire::Disponible->value)
            ->orderBy('code_inventaire')
            ->lockForUpdate()
            ->first();
    }

    public function verrouiller(Exemplaire $exemplaire): Exemplaire
    {
        return $this->query()->lockForUpdate()->findOrFail($exemplaire->id);
    }

    public function prochainCodeInventaire(): string
    {
        $prefixe = config('acrest.bibliotheque.prefixe_inventaire');
        $dernier = $this->query()->where('code_inventaire', 'like', $prefixe.'%')
            ->lockForUpdate()
            ->pluck('code_inventaire')
            ->map(fn ($c) => (int) substr($c, strlen($prefixe)))
            ->max() ?? 0;

        return $prefixe.str_pad((string) ($dernier + 1), 5, '0', STR_PAD_LEFT);
    }
}
