<?php

namespace App\Providers;

use App\Repositories\Contracts\FiliereRepositoryInterface;
use App\Repositories\Contracts\InscriptionRepositoryInterface;
use App\Repositories\Contracts\NewsletterRepositoryInterface;
use App\Repositories\Contracts\PaiementRepositoryInterface;
use App\Repositories\Contracts\SpecialiteRepositoryInterface;
use App\Repositories\Eloquent\FiliereRepository;
use App\Repositories\Eloquent\InscriptionRepository;
use App\Repositories\Eloquent\NewsletterRepository;
use App\Repositories\Eloquent\PaiementRepository;
use App\Repositories\Eloquent\SpecialiteRepository;
use App\Services\Paiement\Contracts\PasserellePaiementInterface;
use App\Services\Paiement\Passerelles\DeclarationManuelle;
use Illuminate\Support\ServiceProvider;

/**
 * Lie chaque interface à son implémentation.
 * Pour changer de source de données ou de passerelle, il suffit de modifier ce tableau.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    public $bindings = [
        FiliereRepositoryInterface::class => FiliereRepository::class,
        SpecialiteRepositoryInterface::class => SpecialiteRepository::class,
        InscriptionRepositoryInterface::class => InscriptionRepository::class,
        PaiementRepositoryInterface::class => PaiementRepository::class,
        NewsletterRepositoryInterface::class => NewsletterRepository::class,
        PasserellePaiementInterface::class => DeclarationManuelle::class,
    ];
}
