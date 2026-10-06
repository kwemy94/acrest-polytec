<?php

namespace App\Http\Controllers\Admin\Bibliotheque;

use App\Enums\EtatPhysique;
use App\Enums\StatutEmprunt;
use App\Exceptions\BibliothequeException;
use App\Http\Controllers\Controller;
use App\Models\Emprunt;
use App\Repositories\Contracts\EmpruntRepositoryInterface;
use App\Services\Bibliotheque\EmpruntService;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Circulation : prêts au guichet, retours, retards et demandes en ligne. */
class EmpruntController extends Controller
{
    public function __construct(private readonly EmpruntService $service)
    {
    }

    public function index(Request $request, EmpruntRepositoryInterface $emprunts): View
    {
        $filtres = $request->only(['q', 'statut', 'retard', 'adherent']);

        return view('admin.bibliotheque.emprunts.index', [
            'emprunts' => $emprunts->rechercher($filtres),
            'filtres' => $filtres,
            'statuts' => StatutEmprunt::cases(),
            'compteurs' => $emprunts->compteurs(),
            'etats' => EtatPhysique::cases(),
        ]);
    }

    /** Nouveau prêt au guichet. */
    public function create(Request $request): View
    {
        return view('admin.bibliotheque.emprunts.create', [
            'matricule' => $request->query('adherent'),
            'exemplaire' => $request->query('exemplaire'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $donnees = $request->validate([
            'matricule' => ['required', 'string', 'max:30'],
            'exemplaire' => ['required', 'string', 'max:50'],
        ], [], ['matricule' => 'matricule de l\'adhérent', 'exemplaire' => 'code de l\'exemplaire']);

        try {
            $emprunt = $this->service->preter($donnees['matricule'], $donnees['exemplaire']);
        } catch (BibliothequeException $e) {
            return back()->withInput()->withErrors(['emprunt' => $e->getMessage()]);
        }

        return redirect()->route('admin.emprunts.create')
            ->with('succes', "Prêt enregistré : {$emprunt->exemplaire->code_inventaire} (« {$emprunt->document->titre} ») à {$emprunt->adherent->nom_complet}, retour prévu le {$emprunt->date_retour_prevue->format('d/m/Y')}.")
            ->withInput(['matricule' => $donnees['matricule']]);
    }

    /** Écran « Retours » : saisie du code de l'exemplaire rendu. */
    public function retours(Request $request): View
    {
        $emprunt = null;
        $erreur = null;

        if ($code = $request->query('code')) {
            try {
                $emprunt = $this->service->empruntEnCoursPourCode($code)->load(['adherent', 'document', 'exemplaire.localisation']);
            } catch (BibliothequeException $e) {
                $erreur = $e->getMessage();
            }
        }

        return view('admin.bibliotheque.emprunts.retours', [
            'code' => $code,
            'emprunt' => $emprunt,
            'erreur' => $erreur,
            'etats' => EtatPhysique::cases(),
        ]);
    }

    public function retour(Request $request, Emprunt $emprunt): RedirectResponse
    {
        $donnees = $request->validate([
            'etat_physique' => ['required', Rule::enum(EtatPhysique::class)],
            'retirer' => ['boolean'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        return $this->executer(function () use ($emprunt, $donnees) {
            $this->service->retourner($emprunt, EtatPhysique::from($donnees['etat_physique']), (bool) ($donnees['retirer'] ?? false), $donnees['note'] ?? null);
            $exemplaire = $emprunt->exemplaire->refresh();

            return "Retour de {$exemplaire->code_inventaire} enregistré"
                .($emprunt->jours_retard ? " avec {$emprunt->jours_retard} jour(s) de retard" : '')
                ." — exemplaire : {$exemplaire->statut->libelle()}.";
        }, route('admin.emprunts.retours'));
    }

    public function perte(Request $request, Emprunt $emprunt): RedirectResponse
    {
        $note = $request->validate(['note' => ['nullable', 'string', 'max:500']])['note'] ?? null;

        return $this->executer(function () use ($emprunt, $note) {
            $this->service->declarerPerte($emprunt, $note);

            return "Exemplaire {$emprunt->exemplaire->code_inventaire} déclaré perdu.";
        });
    }

    public function prolonger(Emprunt $emprunt): RedirectResponse
    {
        return $this->executer(function () use ($emprunt) {
            $this->service->prolonger($emprunt);

            return "Prêt prolongé jusqu'au {$emprunt->date_retour_prevue->format('d/m/Y')}.";
        });
    }

    /** Demandes en ligne : accepter (mettre de côté), remettre au guichet, refuser. */
    public function traiter(Request $request, Emprunt $emprunt, string $action): RedirectResponse
    {
        $motif = $action === 'refuser'
            ? $request->validate(
                ['motif' => ['required', 'string', 'max:500']],
                ['motif.required' => 'Indiquez le motif du refus : il sera communiqué à l\'adhérent.'],
            )['motif']
            : null;

        return $this->executer(function () use ($emprunt, $action, $motif) {
            match ($action) {
                'valider' => $this->service->valider($emprunt),
                'remettre' => $this->service->remettre($emprunt),
                'refuser' => $this->service->refuser($emprunt, $motif),
            };

            return match ($action) {
                'valider' => 'Exemplaire mis de côté, l\'adhérent est prévenu par e-mail.',
                'remettre' => "Prêt enregistré, retour prévu le {$emprunt->date_retour_prevue->format('d/m/Y')}.",
                'refuser' => 'Demande refusée.',
            };
        });
    }

    private function executer(Closure $action, ?string $redirection = null): RedirectResponse
    {
        try {
            $message = $action();
        } catch (BibliothequeException $e) {
            return back()->withErrors(['emprunt' => $e->getMessage()]);
        }

        return ($redirection ? redirect($redirection) : back())->with('succes', $message);
    }
}
