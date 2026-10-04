<?php

namespace App\Repositories\Contracts;

use App\Models\Specialite;
use Illuminate\Database\Eloquent\Collection;

interface SpecialiteRepositoryInterface extends RepositoryInterface
{
    public function findBySlug(string $slug): ?Specialite;

    public function actives(): Collection;

    /** Autres spécialités de la même filière. */
    public function voisines(Specialite $specialite): Collection;

    /**
     * Correspondance filière => spécialités, pour les listes déroulantes dépendantes.
     *
     * @return array<int, array{id:int, nom:string, specialites: array<int, array{id:int, nom:string}>}>
     */
    public function catalogue(): array;

    public function existeActive(int $id): bool;

    public function appartientAFiliere(int $specialiteId, int $filiereId): bool;
}
