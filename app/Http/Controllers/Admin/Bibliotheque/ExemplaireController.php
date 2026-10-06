<?php

namespace App\Http\Controllers\Admin\Bibliotheque;

use App\Enums\EtatPhysique;
use App\Enums\StatutExemplaire;
use App\Exceptions\BibliothequeException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Bibliotheque\ExemplaireRequest;
use App\Models\Document;
use App\Models\Exemplaire;
use App\Models\JournalActivite;
use App\Repositories\Contracts\ExemplaireRepositoryInterface;
use App\Services\Bibliotheque\CatalogueService;
use App\Services\Bibliotheque\PlanBibliotheque;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Exemplaires physiques : inventaire, localisation, statut et historique. */
class ExemplaireController extends Controller
{
    public function __construct(
        private readonly CatalogueService $catalogue,
        private readonly PlanBibliotheque $plan,
    ) {
    }

    public function index(Request $request, ExemplaireRepositoryInterface $exemplaires): View
    {
        $filtres = $request->only(['q', 'statut', 'etat', 'localisation']);

        return view('admin.bibliotheque.exemplaires.index', [
            'exemplaires' => $exemplaires->rechercher($filtres),
            'filtres' => $filtres,
            'statuts' => StatutExemplaire::cases(),
            'etats' => EtatPhysique::cases(),
            'localisations' => $this->plan->options(),
        ]);
    }

    public function store(ExemplaireRequest $request, Document $document): RedirectResponse
    {
        $donnees = $request->validated();
        $crees = $this->catalogue->ajouterExemplaires($document, (int) $donnees['nombre'], collect($donnees)->except('nombre')->all());
        $codes = collect($crees)->pluck('code_inventaire')->join(', ');

        return back()->with('succes', count($crees)." exemplaire(s) ajouté(s) : {$codes}.");
    }

    public function show(Exemplaire $exemplaire): View
    {
        $exemplaire->load([
            'document.auteurs', 'localisation', 'empruntActif.adherent',
            'emprunts.adherent', 'historiqueLocalisations.user', 'historiqueLocalisations.ancienne', 'historiqueLocalisations.nouvelle',
            'incidents.user', 'incidents.adherent',
        ]);

        return view('admin.bibliotheque.exemplaires.show', [
            'exemplaire' => $exemplaire,
            'journal' => JournalActivite::with('user')
                ->where('sujet_type', $exemplaire->getMorphClass())->where('sujet_id', $exemplaire->id)
                ->latest('id')->get(),
            'localisations' => $this->plan->options(),
            'etats' => EtatPhysique::cases(),
            'statutsManuels' => StatutExemplaire::manuels(),
        ]);
    }

    public function update(ExemplaireRequest $request, Exemplaire $exemplaire): RedirectResponse
    {
        $this->catalogue->modifierExemplaire($exemplaire, $request->validated());

        return back()->with('succes', "Exemplaire {$exemplaire->code_inventaire} mis à jour.");
    }

    public function deplacer(Request $request, Exemplaire $exemplaire): RedirectResponse
    {
        $donnees = $request->validate([
            'localisation_id' => ['nullable', 'integer', 'exists:localisations,id'],
            'motif' => ['nullable', 'string', 'max:255'],
        ]);

        $this->catalogue->deplacer($exemplaire, $donnees['localisation_id'] ?? null, $donnees['motif'] ?? null);

        return back()->with('succes', "{$exemplaire->code_inventaire} : ".($exemplaire->localisation?->chemin ?? 'sans localisation').'.');
    }

    public function statut(Request $request, Exemplaire $exemplaire): RedirectResponse
    {
        $donnees = $request->validate([
            'statut' => ['required', Rule::in(array_map(fn ($s) => $s->value, StatutExemplaire::manuels()))],
            'motif' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->catalogue->changerStatut($exemplaire, StatutExemplaire::from($donnees['statut']), $donnees['motif'] ?? null);
        } catch (BibliothequeException $e) {
            return back()->withErrors(['exemplaire' => $e->getMessage()]);
        }

        return back()->with('succes', "{$exemplaire->code_inventaire} : {$exemplaire->statut->libelle()}.");
    }

    public function destroy(Exemplaire $exemplaire): RedirectResponse
    {
        $document = $exemplaire->document;

        try {
            $this->catalogue->supprimerExemplaire($exemplaire);
        } catch (BibliothequeException $e) {
            return back()->withErrors(['exemplaire' => $e->getMessage()]);
        }

        return redirect()->route('admin.documents.show', $document)->with('succes', "Exemplaire {$exemplaire->code_inventaire} supprimé.");
    }
}
