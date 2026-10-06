<?php

namespace App\Http\Controllers\Admin\Bibliotheque;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\TypeDocument;
use App\Services\Bibliotheque\Journal;
use App\Services\Bibliotheque\ParametresPret;
use App\Enums\TypeAdherent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Administration : types de documents, catégories et paramètres de prêt. */
class ReferentielController extends Controller
{
    public function __construct(private readonly Journal $journal)
    {
    }

    public function index(ParametresPret $parametres): View
    {
        return view('admin.bibliotheque.referentiels', [
            'types' => TypeDocument::withCount('documents')->orderBy('ordre')->orderBy('nom')->get(),
            'categories' => Categorie::withCount('documents')->orderBy('nom')->get(),
            'parametres' => $parametres->tous(),
            'typesAdherents' => TypeAdherent::cases(),
        ]);
    }

    /* ---------- Types de documents ---------- */

    public function enregistrerType(Request $request, ?TypeDocument $type = null): RedirectResponse
    {
        $donnees = $request->validate([
            'nom' => ['required', 'string', 'max:60', Rule::unique('types_documents', 'nom')->ignore($type)],
            'ordre' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);
        $donnees['actif'] = $request->boolean('actif', true);
        $donnees['ordre'] ??= 0;

        $type ? $type->update($donnees) : $type = TypeDocument::create($donnees);
        $this->journal->enregistrer('administration', $type, "Type de document « {$type->nom} » enregistré");

        return redirect()->route('admin.referentiels')->with('succes', "Type « {$type->nom} » enregistré.");
    }

    public function supprimerType(TypeDocument $type): RedirectResponse
    {
        if ($type->documents()->exists()) {
            return back()->withErrors(['type' => "Le type « {$type->nom} » est utilisé : désactivez-le plutôt."]);
        }
        $this->journal->enregistrer('administration', null, "Suppression du type de document « {$type->nom} »");
        $type->delete();

        return back()->with('succes', 'Type supprimé.');
    }

    /* ---------- Catégories ---------- */

    public function enregistrerCategorie(Request $request, ?Categorie $categorie = null): RedirectResponse
    {
        $donnees = $request->validate([
            'nom' => ['required', 'string', 'max:100', Rule::unique('categories', 'nom')->ignore($categorie)],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $categorie ? $categorie->update($donnees) : $categorie = Categorie::create($donnees);
        $this->journal->enregistrer('administration', $categorie, "Catégorie « {$categorie->nom} » enregistrée");

        return redirect()->route('admin.referentiels')->with('succes', "Catégorie « {$categorie->nom} » enregistrée.");
    }

    public function supprimerCategorie(Categorie $categorie): RedirectResponse
    {
        $this->journal->enregistrer('administration', null, "Suppression de la catégorie « {$categorie->nom} »");
        $categorie->delete(); // les documents concernés passent « sans catégorie »

        return back()->with('succes', 'Catégorie supprimée.');
    }

    /* ---------- Paramètres de prêt ---------- */

    public function enregistrerParametres(Request $request, ParametresPret $parametres): RedirectResponse
    {
        $regles = [
            'max_prolongations' => ['required', 'integer', 'min:0', 'max:10'],
            'delai_retrait' => ['required', 'integer', 'min:1', 'max:30'],
            'rappel_avant_echeance' => ['required', 'integer', 'min:0', 'max:30'],
            'relance_tous_les' => ['required', 'integer', 'min:1', 'max:60'],
        ];
        foreach (TypeAdherent::cases() as $t) {
            $regles["types.{$t->value}.duree"] = ['required', 'integer', 'min:1', 'max:365'];
            $regles["types.{$t->value}.max"] = ['required', 'integer', 'min:0', 'max:50'];
        }

        $valeurs = $request->validate($regles);
        $parametres->enregistrer($valeurs);
        $this->journal->enregistrer('administration', null, 'Modification des paramètres de prêt', $valeurs);

        return back()->with('succes', 'Paramètres de prêt enregistrés.');
    }
}
