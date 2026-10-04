<?php

namespace App\Repositories\Contracts;

use App\Enums\StatutPaiement;
use App\Models\Paiement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PaiementRepositoryInterface extends RepositoryInterface
{
    public function referenceExiste(string $reference): bool;

    /** @param array{q?:string, statut?:string, operateur?:string} $filtres */
    public function rechercher(array $filtres, int $perPage = 20): LengthAwarePaginator;

    public function changerStatut(Paiement $paiement, StatutPaiement $statut, int $agentId, ?string $note = null): Paiement;

    public function totalEncaisse(): int;

    public function compterEnAttente(): int;
}
