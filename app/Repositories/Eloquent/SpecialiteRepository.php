<?php

namespace App\Repositories\Eloquent;

use App\Models\Filiere;
use App\Models\Specialite;
use App\Repositories\Contracts\SpecialiteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SpecialiteRepository extends BaseRepository implements SpecialiteRepositoryInterface
{
    public function __construct(Specialite $model)
    {
        parent::__construct($model);
    }

    public function findBySlug(string $slug): ?Specialite
    {
        return $this->query()
            ->where('slug', $slug)
            ->where('active', true)
            ->with('filiere')
            ->first();
    }

    public function actives(): Collection
    {
        return $this->query()->actives()->with('filiere')->get();
    }

    public function voisines(Specialite $specialite): Collection
    {
        return $this->query()
            ->actives()
            ->where('filiere_id', $specialite->filiere_id)
            ->whereKeyNot($specialite->getKey())
            ->get();
    }

    public function catalogue(): array
    {
        return Filiere::query()
            ->actives()
            ->with(['specialites' => fn ($q) => $q->where('active', true)->select('id', 'filiere_id', 'nom', 'ordre')])
            ->get(['id', 'nom', 'ordre'])
            ->map(fn (Filiere $f) => [
                'id' => $f->id,
                'nom' => $f->nom,
                'specialites' => $f->specialites->map(fn ($s) => ['id' => $s->id, 'nom' => $s->nom])->values()->all(),
            ])
            ->values()
            ->all();
    }

    public function existeActive(int $id): bool
    {
        return $this->query()->whereKey($id)->where('active', true)->exists();
    }

    public function appartientAFiliere(int $specialiteId, int $filiereId): bool
    {
        return $this->query()->whereKey($specialiteId)->where('filiere_id', $filiereId)->where('active', true)->exists();
    }
}
