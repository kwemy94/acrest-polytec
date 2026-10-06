<?php

namespace App\Repositories\Eloquent;

use App\Enums\StatutEmprunt;
use App\Models\Adherent;
use App\Models\Document;
use App\Models\Emprunt;
use App\Repositories\Contracts\EmpruntRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class EmpruntRepository extends BaseRepository implements EmpruntRepositoryInterface
{
    private const RELATIONS = ['adherent', 'document', 'exemplaire'];

    public function __construct(Emprunt $model)
    {
        parent::__construct($model);
    }

    private function actifs(): array
    {
        return array_map(fn (StatutEmprunt $s) => $s->value, StatutEmprunt::actifs());
    }

    private function enRetard(Builder $q): Builder
    {
        return $q->where('statut', StatutEmprunt::EnCours->value)->whereDate('date_retour_prevue', '<', today());
    }

    public function rechercher(array $filtres, int $perPage = 20): LengthAwarePaginator
    {
        return $this->query()
            ->with([...self::RELATIONS, 'exemplaire.localisation', 'agent'])
            ->when($filtres['q'] ?? null, function (Builder $q, string $terme) {
                $q->where(fn (Builder $w) => $w
                    ->whereHas('document', fn (Builder $d) => $d->where('titre', 'like', "%{$terme}%"))
                    ->orWhereHas('exemplaire', fn (Builder $e) => $e->where('code_inventaire', 'like', "%{$terme}%")->orWhere('code_barres', $terme))
                    ->orWhereHas('adherent', fn (Builder $a) => $a
                        ->where('matricule', 'like', "%{$terme}%")
                        ->orWhere('nom', 'like', "%{$terme}%")
                        ->orWhere('prenom', 'like', "%{$terme}%")));
            })
            ->when($filtres['statut'] ?? null, fn (Builder $q, string $s) => $q->where('statut', $s))
            ->when($filtres['adherent'] ?? null, fn (Builder $q, $a) => $q->where('adherent_id', $a))
            ->when($filtres['retard'] ?? false, fn (Builder $q) => $this->enRetard($q))
            // Les demandes à traiter d'abord, puis les prêts par échéance.
            ->orderByRaw('case statut when ? then 0 when ? then 1 when ? then 2 else 3 end', [
                StatutEmprunt::Demande->value, StatutEmprunt::Reserve->value, StatutEmprunt::EnCours->value,
            ])
            ->orderBy('date_retour_prevue')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function deAdherent(Adherent $adherent): Collection
    {
        return $this->query()
            ->with(['document.auteurs', 'exemplaire'])
            ->where('adherent_id', $adherent->id)
            ->latest()
            ->get();
    }

    public function compterActifs(Adherent $adherent): int
    {
        return $this->query()->where('adherent_id', $adherent->id)->whereIn('statut', $this->actifs())->count();
    }

    public function aDesRetards(Adherent $adherent): bool
    {
        return $this->enRetard($this->query()->where('adherent_id', $adherent->id))->exists();
    }

    public function actifPourDocument(Adherent $adherent, Document $document): ?Emprunt
    {
        return $this->query()
            ->where('adherent_id', $adherent->id)
            ->where('document_id', $document->id)
            ->whereIn('statut', $this->actifs())
            ->first();
    }

    public function prochaineDemande(Document $document): ?Emprunt
    {
        return $this->query()
            ->with(self::RELATIONS)
            ->where('document_id', $document->id)
            ->where('statut', StatutEmprunt::Demande->value)
            ->oldest()
            ->first();
    }

    public function compterDemandes(Document $document): int
    {
        return $this->query()->where('document_id', $document->id)->where('statut', StatutEmprunt::Demande->value)->count();
    }

    public function compteurs(): array
    {
        $parStatut = $this->query()->selectRaw('statut, count(*) as total')->groupBy('statut')->pluck('total', 'statut');

        return [
            'demandes' => (int) ($parStatut[StatutEmprunt::Demande->value] ?? 0),
            'reserves' => (int) ($parStatut[StatutEmprunt::Reserve->value] ?? 0),
            'en_cours' => (int) ($parStatut[StatutEmprunt::EnCours->value] ?? 0),
            'retards' => $this->enRetard($this->query())->count(),
        ];
    }

    public function reservationsExpirees(): Collection
    {
        return $this->query()
            ->with(self::RELATIONS)
            ->where('statut', StatutEmprunt::Reserve->value)
            ->whereDate('retirer_avant', '<', today())
            ->get();
    }

    public function aRappeler(int $joursAvant): Collection
    {
        return $this->query()
            ->with(self::RELATIONS)
            ->where('statut', StatutEmprunt::EnCours->value)
            ->whereNull('rappel_envoye_le')
            ->whereDate('date_retour_prevue', '>=', today())
            ->whereDate('date_retour_prevue', '<=', today()->addDays($joursAvant))
            ->get();
    }

    public function aRelancer(int $intervalle): Collection
    {
        return $this->enRetard($this->query())
            ->with(self::RELATIONS)
            ->where(fn (Builder $q) => $q
                ->whereNull('derniere_relance_le')
                ->orWhere('derniere_relance_le', '<=', now()->subDays($intervalle)->endOfDay()))
            ->get();
    }
}
