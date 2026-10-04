<?php

namespace App\Http\Controllers\Bibliotheque;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Services\Bibliotheque\BibliothequeNumerique;
use App\Services\Bibliotheque\SessionLecteur;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Consultation en ligne des versions PDF.
 * Réservée aux étudiants connectés à la bibliothèque et aux administrateurs.
 */
class LectureController extends Controller
{
    public function __construct(
        private readonly BibliothequeNumerique $numerique,
        private readonly SessionLecteur $session,
    ) {
    }

    public function lire(Document $document): View|RedirectResponse
    {
        return $this->autoriser($document) ?? view('bibliotheque.lire', [
            'document' => $document,
            'lecteur' => $this->session->courant(),
        ]);
    }

    /** Flux PDF affiché dans le lecteur intégré. */
    public function fichier(Document $document): StreamedResponse|RedirectResponse
    {
        return $this->autoriser($document) ?? $this->numerique->afficher($document);
    }

    public function telecharger(Document $document): StreamedResponse|RedirectResponse
    {
        if ($refus = $this->autoriser($document)) {
            return $refus;
        }

        abort_unless($document->telechargeable || auth()->check(), 403, 'Ce document est consultable en ligne uniquement.');

        return $this->numerique->telecharger($document);
    }

    /** Renvoie une redirection si l'accès est refusé, null sinon. */
    private function autoriser(Document $document): ?RedirectResponse
    {
        abort_unless($this->numerique->existe($document), 404);

        if ($this->session->courant() || auth()->check()) {
            return null;
        }

        session()->put('url.intended', route('bibliotheque.lire', $document));

        return redirect()->route('bibliotheque.connexion')
            ->with('info', 'Identifiez-vous avec votre code d\'inscription pour lire les documents numériques.');
    }
}
