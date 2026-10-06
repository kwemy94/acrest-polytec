<?php

namespace App\Http\Controllers\Admin\Bibliotheque;

use App\Enums\TypeLocalisation;
use App\Http\Controllers\Controller;
use App\Models\Exemplaire;
use App\Models\Localisation;
use App\Services\Bibliotheque\Journal;
use App\Services\Bibliotheque\PlanBibliotheque;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Plan de la bibliothèque : espaces imbriqués (salle → rayon → étagère → niveau). */
class LocalisationController extends Controller
{
    public function __construct(
        private readonly PlanBibliotheque $plan,
        private readonly Journal $journal,
    ) {
    }

    public function index(): View
    {
        return view('admin.bibliotheque.localisations.index', [
            'arbre' => $this->plan->arbre(),
            'nombres' => Exemplaire::selectRaw('localisation_id, count(*) as total')->groupBy('localisation_id')->pluck('total', 'localisation_id'),
            'options' => $this->plan->options(),
            'types' => TypeLocalisation::cases(),
        ]);
    }

    private function valider(Request $request, ?Localisation $localisation = null): array
    {
        return $request->validate([
            'nom' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::enum(TypeLocalisation::class)],
            'code' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:500'],
            // Un espace ne peut pas être rattaché à lui-même ni à l'un de ses sous-espaces.
            'parent_id' => ['nullable', 'integer', 'exists:localisations,id',
                Rule::notIn($localisation ? $this->plan->sousArbre($localisation->id) : [])],
        ], ['parent_id.not_in' => 'Un espace ne peut pas être rangé dans lui-même ou dans l\'un de ses sous-espaces.']);
    }

    public function store(Request $request): RedirectResponse
    {
        $localisation = Localisation::create($this->valider($request));
        $this->journal->enregistrer('localisation', $localisation, "Création de l'espace {$localisation->chemin}");

        return back()->with('succes', "Espace « {$localisation->chemin} » créé.");
    }

    public function show(Localisation $localisation): View
    {
        return view('admin.bibliotheque.localisations.show', [
            'localisation' => $localisation->load('enfants'),
            'exemplaires' => Exemplaire::with(['document.auteurs', 'localisation'])
                ->whereIn('localisation_id', $this->plan->sousArbre($localisation->id))
                ->orderBy('code_inventaire')
                ->paginate(30),
            'options' => $this->plan->options($localisation->id),
            'types' => TypeLocalisation::cases(),
        ]);
    }

    public function update(Request $request, Localisation $localisation): RedirectResponse
    {
        $avant = $localisation->chemin;
        $localisation->update($this->valider($request, $localisation));
        $this->journal->enregistrer('localisation', $localisation, "Modification de l'espace {$avant} ⇒ {$localisation->chemin}");

        return back()->with('succes', 'Espace mis à jour.');
    }

    public function destroy(Localisation $localisation): RedirectResponse
    {
        if ($localisation->enfants()->exists() || $localisation->exemplaires()->exists()) {
            return back()->withErrors(['localisation' => 'Cet espace contient des sous-espaces ou des exemplaires : videz-le avant de le supprimer.']);
        }

        $this->journal->enregistrer('localisation', null, "Suppression de l'espace {$localisation->chemin}");
        $localisation->delete();

        return redirect()->route('admin.localisations.index')->with('succes', 'Espace supprimé.');
    }
}
