<?php

namespace App\Repositories\Contracts;

use App\Enums\StatutInscription;
use App\Models\Inscription;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\LazyCollection;

interface InscriptionRepositoryInterface extends RepositoryInterface
{
    public function findByCode(string $code): ?Inscription;

    public function findByEmailEtCni(string $email, string $cni): ?Inscription;

    public function codeExiste(string $code): bool;

    /**
     * Crée l'inscription et ses choix de spécialités (ids ordonnés par rang).
     *
     * @param  array<int, int>  $specialiteIds
     */
    public function creerAvecChoix(array $attributes, array $specialiteIds): Inscription;

    /** @param array{q?:string, filiere?:int, specialite?:int, statut?:string, paiement?:string} $filtres */
    public function rechercher(array $filtres, int $perPage = 20): LengthAwarePaginator;

    /** Flux pour l'export CSV, avec les mêmes filtres que la recherche. */
    public function exporter(array $filtres): LazyCollection;

    public function changerStatut(Inscription $inscription, StatutInscription $statut): Inscription;

    /** @return array<string, int> */
    public function compterParStatut(): array;

    /** Nombre de premiers choix par filière. */
    public function premiersChoixParFiliere(): \Illuminate\Support\Collection;

    public function dernieres(int $limite = 5): \Illuminate\Database\Eloquent\Collection;
}
