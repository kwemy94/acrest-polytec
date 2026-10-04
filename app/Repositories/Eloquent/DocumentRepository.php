<?php

namespace App\Repositories\Eloquent;

use App\Enums\EtatExemplaire;
use App\Models\Document;
use App\Models\Exemplaire;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DocumentRepository extends BaseRepository implements DocumentRepositoryInterface
{
    public function __construct(Document $model)
    {
        parent::__construct($model);
    }

    public function rechercher(array $filtres, int $perPage = 12): LengthAwarePaginator
    {
        return $this->query()
            ->with('filiere')
            ->avecDisponibilite()
            ->when($filtres['q'] ?? null, function (Builder $q, string $terme) {
                $q->where(fn (Builder $w) => $w
                    ->where('titre', 'like', "%{$terme}%")
                    ->orWhere('auteurs', 'like', "%{$terme}%")
                    ->orWhere('isbn', 'like', "%{$terme}%")
                    ->orWhere('cote', 'like', "%{$terme}%")
                    ->orWhereHas('exemplaires', fn (Builder $e) => $e->where('code', $terme)));
            })
            ->when($filtres['type'] ?? null, fn (Builder $q, string $t) => $q->where('type', $t))
            ->when($filtres['langue'] ?? null, fn (Builder $q, string $l) => $q->where('langue', $l))
            ->when($filtres['numerique'] ?? false, fn (Builder $q) => $q->whereNotNull('fichier'))
            ->when($filtres['filiere'] ?? null, fn (Builder $q, $f) => $q->where('filiere_id', $f))
            ->when($filtres['disponible'] ?? false, fn (Builder $q) => $q
                ->where('consultation_sur_place', false)
                ->whereNull('fichier')
                ->whereHas('exemplaires', fn (Builder $e) => $e->where('etat', EtatExemplaire::Disponible->value)))
            ->orderBy('titre')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function detail(int $id): ?Document
    {
        return $this->query()->with(['filiere', 'exemplaires'])->avecDisponibilite()->find($id);
    }

    public function creerAvecExemplaires(array $attributes, int $nombre): Document
    {
        return DB::transaction(function () use ($attributes, $nombre) {
            $document = $this->create($attributes);
            $this->ajouterExemplaires($document, $nombre);

            return $document;
        });
    }

    public function ajouterExemplaires(Document $document, int $nombre): void
    {
        for ($i = 0; $i < $nombre; $i++) {
            $exemplaire = $document->exemplaires()->create(['etat' => EtatExemplaire::Disponible]);
            // Code-barres à coller sur l'exemplaire, dérivé de l'identifiant.
            $exemplaire->update(['code' => 'EX'.str_pad((string) $exemplaire->id, 6, '0', STR_PAD_LEFT)]);
        }
    }

    public function exemplaireDisponible(Document $document): ?Exemplaire
    {
        return Exemplaire::where('document_id', $document->id)
            ->where('etat', EtatExemplaire::Disponible->value)
            ->orderBy('id')
            ->lockForUpdate()
            ->first();
    }

    public function exemplaireParCode(string $code): ?Exemplaire
    {
        return Exemplaire::with('document')->where('code', strtoupper(trim($code)))->first();
    }

    public function statistiques(): array
    {
        return [
            'documents' => $this->count(),
            'exemplaires' => Exemplaire::where('etat', '!=', EtatExemplaire::Indisponible->value)->count(),
            'disponibles' => Exemplaire::where('etat', EtatExemplaire::Disponible->value)->count(),
        ];
    }
}
