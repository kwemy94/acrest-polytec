<?php

namespace App\Repositories\Eloquent;

use App\Enums\StatutInscription;
use App\Enums\StatutPaiement;
use App\Models\Inscription;
use App\Repositories\Contracts\InscriptionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;

class InscriptionRepository extends BaseRepository implements InscriptionRepositoryInterface
{
    public function __construct(Inscription $model)
    {
        parent::__construct($model);
    }

    public function findByCode(string $code): ?Inscription
    {
        return $this->query()
            ->where('code', strtoupper(trim($code)))
            ->with(['specialites.filiere', 'paiements'])
            ->first();
    }

    public function findByEmailEtCni(string $email, string $cni): ?Inscription
    {
        return $this->query()
            ->where('email', mb_strtolower(trim($email)))
            ->where('cni', trim($cni))
            ->latest()
            ->first();
    }

    public function codeExiste(string $code): bool
    {
        return $this->query()->withTrashed()->where('code', $code)->exists();
    }

    public function creerAvecChoix(array $attributes, array $specialiteIds): Inscription
    {
        return DB::transaction(function () use ($attributes, $specialiteIds) {
            /** @var Inscription $inscription */
            $inscription = $this->query()->create($attributes);

            $choix = [];
            foreach (array_values($specialiteIds) as $index => $id) {
                $choix[$id] = ['rang' => $index + 1];
            }
            $inscription->specialites()->attach($choix);

            return $inscription->load('specialites.filiere');
        });
    }

    protected function filtrer(array $filtres): Builder
    {
        return $this->query()
            ->with(['specialites.filiere', 'dernierPaiement'])
            ->when($filtres['q'] ?? null, function (Builder $q, string $terme) {
                $q->where(fn (Builder $w) => $w
                    ->where('nom', 'like', "%{$terme}%")
                    ->orWhere('prenom', 'like', "%{$terme}%")
                    ->orWhere('code', 'like', "%{$terme}%")
                    ->orWhere('email', 'like', "%{$terme}%")
                    ->orWhere('telephone', 'like', "%{$terme}%")
                    ->orWhere('cni', 'like', "%{$terme}%"));
            })
            ->when($filtres['statut'] ?? null, fn (Builder $q, string $s) => $q->where('statut', $s))
            ->when($filtres['specialite'] ?? null, fn (Builder $q, $id) => $q->whereHas(
                'specialites', fn (Builder $s) => $s->where('specialites.id', $id)
            ))
            ->when($filtres['filiere'] ?? null, fn (Builder $q, $id) => $q->whereHas(
                'specialites', fn (Builder $s) => $s->where('filiere_id', $id)->where('inscription_choix.rang', 1)
            ))
            ->when(($filtres['paiement'] ?? null) === 'paye', fn (Builder $q) => $q->whereHas(
                'paiements', fn (Builder $p) => $p->where('statut', StatutPaiement::Valide->value)
            ))
            ->when(($filtres['paiement'] ?? null) === 'non_paye', fn (Builder $q) => $q->whereDoesntHave(
                'paiements', fn (Builder $p) => $p->where('statut', StatutPaiement::Valide->value)
            ))
            ->latest();
    }

    public function rechercher(array $filtres, int $perPage = 20): LengthAwarePaginator
    {
        return $this->filtrer($filtres)->paginate($perPage)->withQueryString();
    }

    public function exporter(array $filtres): LazyCollection
    {
        return $this->filtrer($filtres)->with('paiements')->lazy(200);
    }

    public function changerStatut(Inscription $inscription, StatutInscription $statut): Inscription
    {
        $inscription->update(['statut' => $statut]);

        return $inscription;
    }

    public function compterParStatut(): array
    {
        $comptes = $this->query()
            ->select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut');

        $resultat = [];
        foreach (StatutInscription::cases() as $statut) {
            $resultat[$statut->value] = (int) ($comptes[$statut->value] ?? 0);
        }

        return $resultat;
    }

    public function premiersChoixParFiliere(): SupportCollection
    {
        return DB::table('inscription_choix')
            ->join('inscriptions', 'inscriptions.id', '=', 'inscription_choix.inscription_id')
            ->join('specialites', 'specialites.id', '=', 'inscription_choix.specialite_id')
            ->join('filieres', 'filieres.id', '=', 'specialites.filiere_id')
            ->whereNull('inscriptions.deleted_at')
            ->where('inscription_choix.rang', 1)
            ->select('filieres.nom', DB::raw('count(*) as total'))
            ->groupBy('filieres.id', 'filieres.nom')
            ->orderByDesc('total')
            ->get();
    }

    public function dernieres(int $limite = 5): Collection
    {
        return $this->query()->with(['specialites', 'dernierPaiement'])->latest()->limit($limite)->get();
    }
}
