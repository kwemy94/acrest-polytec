<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatutEmprunt;
use App\Exceptions\BibliothequeException;
use App\Http\Controllers\Controller;
use App\Models\Emprunt;
use App\Repositories\Contracts\EmpruntRepositoryInterface;
use App\Services\Bibliotheque\EmpruntService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Circulation : traitement des demandes, prêts au guichet et retours. */
class EmpruntController extends Controller
{
    public function __construct(private readonly EmpruntService $service)
    {
    }

    public function index(Request $request, EmpruntRepositoryInterface $emprunts): View
    {
        $filtres = $request->only(['q', 'statut', 'retard']);

        return view('admin.emprunts.index', [
            'emprunts' => $emprunts->rechercher($filtres),
            'filtres' => $filtres,
            'statuts' => StatutEmprunt::cases(),
            'compteurs' => $emprunts->compteurs(),
        ]);
    }

    public function create(): View
    {
        return view('admin.emprunts.create');
    }

    /** Prêt direct au guichet. */
    public function store(Request $request): RedirectResponse
    {
        $donnees = $request->validate([
            'code' => ['required', 'string', 'max:20'],
            'exemplaire' => ['required', 'string', 'max:20'],
        ], [], ['code' => 'code d\'inscription', 'exemplaire' => 'code de l\'exemplaire']);

        try {
            $emprunt = $this->service->pretDirect($donnees['code'], $donnees['exemplaire'], $request->user()->id);
        } catch (BibliothequeException $e) {
            return back()->withInput()->withErrors(['emprunt' => $e->getMessage()]);
        }

        return redirect()->route('admin.emprunts.index')
            ->with('succes', "Prêt enregistré : « {$emprunt->document->titre} » à rendre le {$emprunt->date_retour_prevue->format('d/m/Y')}.");
    }

    public function traiter(Request $request, Emprunt $emprunt, string $action): RedirectResponse
    {
        $agent = $request->user()->id;

        $motif = $action === 'refuser'
            ? $request->validate(
                ['motif' => ['required', 'string', 'max:500']],
                ['motif.required' => 'Indiquez le motif du refus : il sera communiqué à l\'étudiant.'],
            )['motif']
            : null;

        try {
            match ($action) {
                'valider' => $this->service->valider($emprunt, $agent),
                'refuser' => $this->service->refuser($emprunt, $agent, $motif),
                'remettre' => $this->service->remettre($emprunt, $agent),
                'retour' => $this->service->retourner($emprunt, $agent, $request->boolean('hors_service')),
                'prolonger' => $this->service->prolonger($emprunt, $agent),
            };
        } catch (BibliothequeException $e) {
            return back()->withErrors(['emprunt' => $e->getMessage()]);
        }

        $message = match ($action) {
            'valider' => 'exemplaire mis de côté, l\'étudiant est prévenu par e-mail.',
            'refuser' => 'demande refusée.',
            'remettre' => "prêt enregistré, retour prévu le {$emprunt->date_retour_prevue->format('d/m/Y')}.",
            'retour' => 'retour enregistré.'.($emprunt->joursDeRetard() ? " Retard : {$emprunt->joursDeRetard()} jour(s)." : ''),
            'prolonger' => "prêt prolongé jusqu'au {$emprunt->date_retour_prevue->format('d/m/Y')}.",
        };

        return back()->with('succes', "« {$emprunt->document->titre} » : {$message}");
    }
}
