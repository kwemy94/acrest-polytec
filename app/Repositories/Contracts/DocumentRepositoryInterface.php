<?php

namespace App\Repositories\Contracts;

use App\Models\Document;
use App\Models\Exemplaire;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DocumentRepositoryInterface extends RepositoryInterface
{
    /**
     * Catalogue avec disponibilité (public et administration).
     *
     * @param  array{q?:string, type?:string, langue?:string, filiere?:int, disponible?:bool, numerique?:bool}  $filtres
     */
    public function rechercher(array $filtres, int $perPage = 12): LengthAwarePaginator;

    /** Notice avec ses exemplaires, sa filière et ses compteurs de disponibilité. */
    public function detail(int $id): ?Document;

    /** Crée la notice et ses premiers exemplaires. */
    public function creerAvecExemplaires(array $attributes, int $nombre): Document;

    public function ajouterExemplaires(Document $document, int $nombre): void;

    /** Premier exemplaire disponible, verrouillé pour la transaction en cours. */
    public function exemplaireDisponible(Document $document): ?Exemplaire;

    public function exemplaireParCode(string $code): ?Exemplaire;

    /** @return array{documents:int, exemplaires:int, disponibles:int} */
    public function statistiques(): array;
}
