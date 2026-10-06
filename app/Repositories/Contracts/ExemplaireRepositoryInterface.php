<?php

namespace App\Repositories\Contracts;

use App\Models\Document;
use App\Models\Exemplaire;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ExemplaireRepositoryInterface extends RepositoryInterface
{
    /** @param array{q?:string, statut?:string, etat?:string, localisation?:int} $filtres */
    public function rechercher(array $filtres, int $perPage = 25): LengthAwarePaginator;

    /** Recherche par code d'inventaire ou code-barres. */
    public function parCode(string $code): ?Exemplaire;

    /** Premier exemplaire disponible du document, verrouillé pour la transaction en cours. */
    public function disponiblePour(Document $document): ?Exemplaire;

    public function verrouiller(Exemplaire $exemplaire): Exemplaire;

    public function prochainCodeInventaire(): string;
}
