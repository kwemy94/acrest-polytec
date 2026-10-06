<?php

namespace App\Http\Controllers\Bibliotheque;

use App\Http\Controllers\Controller;
use App\Models\RessourceNumerique;
use App\Services\Bibliotheque\BibliothequeNumerique;
use App\Services\Bibliotheque\SessionLecteur;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Consultation et téléchargement des ressources numériques, selon leur niveau d'accès :
 * personnel connecté, ou adhérent actif connecté à l'espace bibliothèque.
 */
class RessourceController extends Controller
{
    public function __construct(
        private readonly BibliothequeNumerique $numerique,
        private readonly SessionLecteur $session,
    ) {
    }

    public function consulter(RessourceNumerique $ressource): View|RedirectResponse
    {
        if ($refus = $this->identifier($ressource)) {
            return $refus;
        }
        $this->exiger($this->numerique->peutConsulter($ressource, auth()->user(), $this->session->courant()), 'Cette ressource n\'est pas consultable en ligne.');

        return view('bibliotheque.consulter', [
            'ressource' => $ressource->load('document.auteurs'),
            'telechargeable' => $this->numerique->peutTelecharger($ressource, auth()->user(), $this->session->courant()),
            'lecteur' => $this->session->courant(),
        ]);
    }

    /** Flux du fichier affiché dans la page de consultation. */
    public function fichier(RessourceNumerique $ressource): BinaryFileResponse|RedirectResponse
    {
        if ($refus = $this->identifier($ressource)) {
            return $refus;
        }
        $this->exiger($this->numerique->peutConsulter($ressource, auth()->user(), $this->session->courant()), 'Cette ressource n\'est pas consultable en ligne.');

        return $this->numerique->afficher($ressource);
    }

    public function telecharger(RessourceNumerique $ressource): BinaryFileResponse|RedirectResponse
    {
        if ($refus = $this->identifier($ressource)) {
            return $refus;
        }
        $this->exiger($this->numerique->peutTelecharger($ressource, auth()->user(), $this->session->courant()), 'Le téléchargement de cette ressource n\'est pas autorisé.');

        return $this->numerique->telecharger($ressource);
    }

    /** Redirige vers la connexion si personne n'est identifié. */
    private function identifier(RessourceNumerique $ressource): ?RedirectResponse
    {
        abort_unless($this->numerique->existe($ressource), 404);

        if (auth()->check() || $this->session->courant()) {
            return null;
        }

        session()->put('url.intended', route('bibliotheque.show', $ressource->document_id));

        return redirect()->route('bibliotheque.connexion')
            ->with('info', 'Identifiez-vous avec votre matricule pour accéder aux ressources numériques.');
    }

    private function exiger(bool $autorise, string $message): void
    {
        $adherent = $this->session->courant();
        if (! $autorise && $adherent && ! auth()->check() && ! $adherent->estActif()) {
            $message = "Votre adhésion est « {$adherent->statutEffectif()->libelle()} » : l'accès aux ressources numériques est suspendu.";
        }

        abort_unless($autorise, 403, $message);
    }
}
