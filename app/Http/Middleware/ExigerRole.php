<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Restreint une route à certains rôles : ->middleware('role:admin') ou 'role:admin,bibliothecaire'. */
class ExigerRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->aRole(...$roles)) {
            abort(403, 'Cette section est réservée aux administrateurs.');
        }

        return $next($request);
    }
}
