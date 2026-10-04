<?php

namespace App\Http\Controllers\Bibliotheque;

use App\Http\Controllers\Controller;
use App\Http\Requests\RechercheDossierRequest;
use App\Repositories\Contracts\InscriptionRepositoryInterface;
use App\Services\Bibliotheque\SessionLecteur;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Connexion de l'étudiant à l'espace bibliothèque, avec son code d'inscription et son e-mail. */
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

    public function store(RechercheDossierRequest $request, InscriptionRepositoryInterface $inscriptions): RedirectResponse
    {
        $inscription = $inscriptions->findByCode($request->validated('code'));

        if (! $inscription || $inscription->email !== $request->validated('email')) {
            return back()->withInput()->withErrors([
                'code' => 'Aucun étudiant ne correspond à ce code et à cette adresse e-mail.',
            ]);
        }

        if (! $inscription->peutEmprunter()) {
            return back()->withInput()->withErrors([
                'code' => 'Le prêt est réservé aux étudiants dont le dossier d\'inscription est validé. Votre dossier est « '.$inscription->statut->libelle().' ».',
            ]);
        }

        $this->session->connecter($inscription);

        return redirect()->intended(route('bibliotheque.emprunts'))
            ->with('succes', 'Bienvenue '.($inscription->prenom ?: $inscription->nom).' ! Vous pouvez maintenant demander des documents.');
    }

    public function destroy(): RedirectResponse
    {
        $this->session->deconnecter();

        return redirect()->route('bibliotheque.index')->with('info', 'Vous êtes déconnecté de l\'espace bibliothèque.');
    }
}
