<?php

namespace App\Repositories\Contracts;

use App\Models\Document;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DocumentRepositoryInterface extends RepositoryInterface
{
    /**
     * Recherche dans le catalogue, avec disponibilité et localisations des exemplaires.
     *
     * @param  array{q?:string, auteur?:string, mot_cle?:string, isbn?:string, type?:int, categorie?:int,
     *               langue?:string, localisation?:int, disponible?:bool, numerique?:bool}  $filtres
     */
    public function rechercher(array $filtres, int $perPage = 12): LengthAwarePaginator;

    /** Notice complète : auteurs, type, catégorie, exemplaires (avec localisation), ressources, compteurs. */
    public function detail(int $id): ?Document;

    /**
     * Crée ou met à jour une notice et ses auteurs (dans l'ordre donné).
     *
     * @param  list<string>  $auteurs
     */
    public function enregistrer(Document $document, array $attributes, array $auteurs): Document;

    /** @return array{documents:int, exemplaires:int, disponibles:int, empruntes:int, ressources:int} */
    public function statistiques(): array;
}
