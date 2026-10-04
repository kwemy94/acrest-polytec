<?php

namespace App\Repositories\Eloquent;

use App\Models\Filiere;
use App\Repositories\Contracts\FiliereRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class FiliereRepository extends BaseRepository implements FiliereRepositoryInterface
{
    public function __construct(Filiere $model)
    {
        parent::__construct($model);
    }

    public function activesAvecSpecialites(): Collection
    {
        return $this->query()
            ->actives()
            ->with(['specialites' => fn ($q) => $q->where('active', true)])
            ->withCount(['specialites' => fn ($q) => $q->where('active', true)])
            ->get();
    }

    public function findBySlug(string $slug): ?Filiere
    {
        return $this->query()
            ->where('slug', $slug)
            ->where('active', true)
            ->with(['specialites' => fn ($q) => $q->where('active', true)])
            ->first();
    }

    public function parDomaine(): SupportCollection
    {
        return $this->activesAvecSpecialites()->groupBy('domaine');
    }
}
