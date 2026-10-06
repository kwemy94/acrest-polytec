<?php

namespace App\Providers;

use App\Repositories\Contracts\FiliereRepositoryInterface;
use App\Services\Bibliotheque\PlanBibliotheque;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(PlanBibliotheque::class);
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Limite les recherches de dossier (code, e-mail) pour empêcher l'énumération.
        RateLimiter::for('recherche-dossier', fn (Request $request) => Limit::perMinute(8)->by($request->ip()));

        // Menu "Formations" partagé par toutes les pages publiques.
        View::composer('partials.navbar', function ($view) {
            try {
                $filieres = Cache::remember('menu.filieres', 3600, fn () => app(FiliereRepositoryInterface::class)
                    ->activesAvecSpecialites()
                    ->map->only(['nom', 'slug'])
                    ->all());
            } catch (Throwable) {
                $filieres = [];
            }
            $view->with('menuFilieres', $filieres);
        });
    }
}
