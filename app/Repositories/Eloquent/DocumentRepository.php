<?php

namespace App\Repositories\Eloquent;

use App\Enums\NiveauAcces;
use App\Enums\StatutExemplaire;
use App\Models\Auteur;
use App\Models\Document;
use App\Models\Exemplaire;
use App\Models\RessourceNumerique;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use App\Services\Bibliotheque\PlanBibliotheque;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DocumentRepository extends BaseRepository implements DocumentRepositoryInterface
{
    public function __construct(Document $model, private readonly PlanBibliotheque $plan)
    {
        parent::__construct($model);
    }

    public function rechercher(array $filtres, int $perPage = 12): LengthAwarePaginator
    {
        $fondsActif = array_map(fn ($s) => $s->value, StatutExemplaire::fondsActif());

        return $this->query()
            ->with([
                'type', 'categorie', 'auteurs',
                'exemplaires' => fn ($q) => $q->select('id', 'document_id', 'code_inventaire', 'statut', 'localisation_id')
                    ->whereIn('statut', $fondsActif),
            ])
            ->avecDisponibilite()
            ->when($filtres['q'] ?? null, function (Builder $q, string $terme) {
                $q->where(fn (Builder $w) => $w
                    ->where('titre', 'like', "%{$terme}%")
                    ->orWhere('sous_titre', 'like', "%{$terme}%")
                    ->orWhere('isbn', 'like', '%'.preg_replace('/[^0-9Xx]/', '', $terme).'%')
                    ->orWhere('mots_cles', 'like', "%{$terme}%")
                    ->orWhere('cote', 'like', "%{$terme}%")
                    ->orWhereHas('auteurs', fn (Builder $a) => $a->where('nom', 'like', "%{$terme}%"))
                    ->orWhereHas('exemplaires', fn (Builder $e) => $e->where('code_inventaire', $terme)->orWhere('code_barres', $terme)));
            })
            ->when($filtres['auteur'] ?? null, fn (Builder $q, string $a) => $q->whereHas('auteurs', fn (Builder $w) => $w->where('nom', 'like', "%{$a}%")))
            ->when($filtres['mot_cle'] ?? null, fn (Builder $q, string $m) => $q->where('mots_cles', 'like', "%{$m}%"))
            ->when($filtres['isbn'] ?? null, fn (Builder $q, string $i) => $q->where('isbn', 'like', '%'.preg_replace('/[^0-9Xx]/', '', $i).'%'))
            ->when($filtres['type'] ?? null, fn (Builder $q, $t) => $q->where('type_document_id', $t))
            ->when($filtres['categorie'] ?? null, fn (Builder $q, $c) => $q->where('categorie_id', $c))
            ->when($filtres['langue'] ?? null, fn (Builder $q, string $l) => $q->where('langue', $l))
            ->when($filtres['localisation'] ?? null, fn (Builder $q, $l) => $q->whereHas('exemplaires', fn (Builder $e) => $e
                ->whereIn('localisation_id', $this->plan->sousArbre((int) $l))))
            ->when($filtres['disponible'] ?? false, fn (Builder $q) => $q
                ->where('consultation_sur_place', false)
                ->whereHas('exemplaires', fn (Builder $e) => $e->where('statut', StatutExemplaire::Disponible->value)))
            ->when($filtres['numerique'] ?? false, fn (Builder $q) => $q
                ->whereHas('ressources', fn (Builder $r) => $r->where('niveau_acces', '!=', NiveauAcces::Restreint->value)))
            ->orderBy('titre')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function detail(int $id): ?Document
    {
        return $this->query()
            ->with(['type', 'categorie', 'auteurs', 'ressources', 'exemplaires.localisation', 'exemplaires.empruntActif.adherent'])
            ->avecDisponibilite()
            ->find($id);
    }

    public function enregistrer(Document $document, array $attributes, array $auteurs): Document
    {
        return DB::transaction(function () use ($document, $attributes, $auteurs) {
            $document->fill($attributes)->save();

            $ids = collect($auteurs)
                ->map(fn ($nom) => trim(preg_replace('/\s+/', ' ', $nom)))
                ->filter()
                ->unique(fn ($nom) => mb_strtolower($nom))
                ->values()
                ->mapWithKeys(fn ($nom, $i) => [Auteur::firstOrCreate(['nom' => $nom])->id => ['ordre' => $i + 1]]);
            $document->auteurs()->sync($ids->all());

            return $document->load('auteurs');
        });
    }

    public function statistiques(): array
    {
        $parStatut = Exemplaire::selectRaw('statut, count(*) as total')->groupBy('statut')->pluck('total', 'statut');
        $fondsActif = array_map(fn ($s) => $s->value, StatutExemplaire::fondsActif());

        return [
            'documents' => $this->count(),
            'exemplaires' => (int) $parStatut->only($fondsActif)->sum(),
            'disponibles' => (int) ($parStatut[StatutExemplaire::Disponible->value] ?? 0),
            'empruntes' => (int) ($parStatut[StatutExemplaire::Emprunte->value] ?? 0),
            'ressources' => RessourceNumerique::distinct()->count('document_id'),
        ];
    }
}
