<?php

namespace App\Repositories\Contracts;

use App\Models\Adherent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AdherentRepositoryInterface extends RepositoryInterface
{
    /** @param array{q?:string, type?:string, statut?:string} $filtres */
    public function rechercher(array $filtres, int $perPage = 25): LengthAwarePaginator;

    public function parMatricule(string $matricule): ?Adherent;

    public function compterActifs(): int;

    /** Passe à « expiré » les adhérents actifs dont la date d'expiration est dépassée. */
    public function expirerEchus(): int;
}
