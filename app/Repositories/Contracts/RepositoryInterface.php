<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Contrat commun à tous les repositories.
 *
 * @template TModel of Model
 */
interface RepositoryInterface
{
    /** @return Collection<int, TModel> */
    public function all(array $relations = []): Collection;

    /** @return TModel|null */
    public function find(int $id, array $relations = []): ?Model;

    /** @return TModel */
    public function findOrFail(int $id, array $relations = []): Model;

    /** @return TModel */
    public function create(array $attributes): Model;

    public function update(Model $model, array $attributes): Model;

    public function delete(Model $model): bool;

    public function paginate(int $perPage = 15, array $relations = []): LengthAwarePaginator;

    public function count(): int;
}
