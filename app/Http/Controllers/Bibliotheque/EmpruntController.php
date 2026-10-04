<?php

namespace App\Http\Controllers\Bibliotheque;

use App\Exceptions\BibliothequeException;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Emprunt;
use App\Repositories\Contracts\EmpruntRepositoryInterface;
use App\Services\Bibliotheque\EmpruntService;
use App\Services\Bibliotheque\SessionLecteur;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Espace « Mes emprunts » de l'étudiant. */
class EmpruntController extends Controller
{
    public function __construct(
        private readonly EmpruntService $service,
        private readonly SessionLecteur $session,
    ) {
    }

    public function index(EmpruntRepositoryInterface $emprunts): View
    {
        $lecteur = $this->session->courant();
        $liste = $emprunts->duLecteur($lecteur);

        return view('bibliotheque.emprunts', [
            'lecteur' => $lecteur,
            'actifs' => $liste->filter(fn (Emprunt $e) => $e->statut->estActif()),
            'historique' => $liste->reject(fn (Emprunt $e) => $e->statut->estActif()),
        ]);
    }

    public function store(Request $request, Document $document): RedirectResponse
    {
        $message = $request->validate(['message' => ['nullable', 'string', 'max:500']])['message'] ?? null;

        try {
            $this->service->demander($this->session->courant(), $document, $message);
        } catch (BibliothequeException $e) {
            return back()->withErrors(['emprunt' => $e->getMessage()]);
        }

        return redirect()->route('bibliotheque.emprunts')
            ->with('succes', "Votre demande pour « {$document->titre} » est enregistrée. Vous recevrez un e-mail dès que le document sera prêt à être retiré.");
    }

    public function annuler(Emprunt $emprunt): RedirectResponse
    {
        return $this->executer($emprunt, fn () => $this->service->annuler($emprunt), 'Demande annulée.');
    }

    public function prolonger(Emprunt $emprunt): RedirectResponse
    {
        return $this->executer(
            $emprunt,
            fn () => $this->service->prolonger($emprunt),
            fn () => 'Prêt prolongé jusqu\'au '.$emprunt->date_retour_prevue->translatedFormat('d F Y').'.',
        );
    }

    private function executer(Emprunt $emprunt, Closure $action, string|Closure $succes): RedirectResponse
    {
        // Un étudiant n'agit que sur ses propres emprunts.
        abort_unless((int) $emprunt->inscription_id === (int) $this->session->courant()->id, 404);

        try {
            $action();
        } catch (BibliothequeException $e) {
            return back()->withErrors(['emprunt' => $e->getMessage()]);
        }

        return back()->with('succes', $succes instanceof Closure ? $succes() : $succes);
    }
}
