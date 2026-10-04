<?php

namespace App\Repositories\Contracts;

use App\Models\NewsletterAbonne;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface NewsletterRepositoryInterface extends RepositoryInterface
{
    /** Inscrit l'adresse si elle est nouvelle. Retourne false si déjà abonnée. */
    public function abonner(string $email): bool;

    public function rechercher(?string $q, int $perPage = 30): LengthAwarePaginator;

    /** @return array<int, string> */
    public function emails(): array;
}
