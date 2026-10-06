<?php

namespace App\Http\Controllers\Bibliotheque;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\AdherentRepositoryInterface;
use App\Services\Bibliotheque\SessionLecteur;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/** Connexion de l'adhérent à l'espace bibliothèque, avec son matricule et son e-mail. */
class LecteurController extends Controller
{
    public function __construct(private readonly SessionLecteur $session)
    {
    }

    public function create(): View|RedirectResponse
    {
        return $this->session->courant()
            ? redirect()->route('bibliotheque.emprunts')
            : view('bibliotheque.connexion');
    }

    public function store(Request $request, AdherentRepositoryInterface $adherents): RedirectResponse
    {
        $request->merge([
            'matricule' => Str::upper(trim((string) $request->matricule)),
            'email' => Str::lower(trim((string) $request->email)),
        ]);
        $donnees = $request->validate([
            'matricule' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email'],
        ]);

        $adherent = $adherents->parMatricule($donnees['matricule']);

        if (! $adherent || ! $adherent->email || Str::lower($adherent->email) !== $donnees['email']) {
            return back()->withInput()->withErrors([
                'matricule' => 'Aucun adhérent ne correspond à ce matricule et à cette adresse e-mail.',
            ]);
        }

        $this->session->connecter($adherent);

        return redirect()->intended(route('bibliotheque.emprunts'))
            ->with('succes', 'Bienvenue '.($adherent->prenom ?: $adherent->nom).' !');
    }

    public function destroy(): RedirectResponse
    {
        $this->session->deconnecter();

        return redirect()->route('bibliotheque.index')->with('info', 'Vous êtes déconnecté de l\'espace bibliothèque.');
    }
}
