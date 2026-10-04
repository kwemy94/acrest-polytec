<?php

namespace App\Repositories\Contracts;

use App\Models\Filiere;
use Illuminate\Database\Eloquent\Collection;

interface FiliereRepositoryInterface extends RepositoryInterface
{
    /** Filières actives avec leurs spécialités actives. */
    public function activesAvecSpecialites(): Collection;

    public function findBySlug(string $slug): ?Filiere;

    /** Filières regroupées par domaine. */
    public function parDomaine(): \Illuminate\Support\Collection;
}
