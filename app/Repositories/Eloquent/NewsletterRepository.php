<?php

namespace App\Repositories\Eloquent;

use App\Models\NewsletterAbonne;
use App\Repositories\Contracts\NewsletterRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NewsletterRepository extends BaseRepository implements NewsletterRepositoryInterface
{
    public function __construct(NewsletterAbonne $model)
    {
        parent::__construct($model);
    }

    public function abonner(string $email): bool
    {
        return $this->query()->firstOrCreate(['email' => mb_strtolower(trim($email))])->wasRecentlyCreated;
    }

    public function rechercher(?string $q, int $perPage = 30): LengthAwarePaginator
    {
        return $this->query()
            ->when($q, fn ($query) => $query->where('email', 'like', "%{$q}%"))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function emails(): array
    {
        return $this->query()->orderBy('email')->pluck('email')->all();
    }
}
