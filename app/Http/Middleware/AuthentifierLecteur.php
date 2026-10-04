<?php

namespace App\Http\Middleware;

use App\Services\Bibliotheque\SessionLecteur;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Réserve l'espace emprunts aux étudiants connectés à la bibliothèque. */
class AuthentifierLecteur
{
    public function __construct(private readonly SessionLecteur $session)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->session->courant()) {
            if ($request->isMethod('get')) {
                $request->session()->put('url.intended', $request->fullUrl());
            }

            return redirect()->route('bibliotheque.connexion')
                ->with('info', 'Identifiez-vous avec votre code d\'inscription pour emprunter un document.');
        }

        return $next($request);
    }
}
