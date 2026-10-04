<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EtatExemplaire;
use App\Enums\TypeDocument;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DocumentRequest;
use App\Models\Document;
use App\Models\Exemplaire;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use App\Repositories\Contracts\FiliereRepositoryInterface;
use App\Services\Bibliotheque\BibliothequeNumerique;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Catalogue de la bibliothèque : notices et exemplaires. */
class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentRepositoryInterface $documents,
        private readonly BibliothequeNumerique $numerique,
    ) {
    }

    public function index(Request $request): View
    {
        $filtres = $request->only(['q', 'type', 'langue', 'numerique']);

        return view('admin.documents.index', [
            'documents' => $this->documents->rechercher($filtres, 20),
            'filtres' => $filtres,
            'types' => TypeDocument::cases(),
            'stats' => $this->documents->statistiques(),
        ]);
    }

    public function create(FiliereRepositoryInterface $filieres): View
    {
        return view('admin.documents.form', ['document' => new Document(), 'filieres' => $filieres->activesAvecSpecialites()]);
    }

    public function store(DocumentRequest $request): RedirectResponse
    {
        $document = $this->documents->creerAvecExemplaires($request->notice(), (int) $request->validated('exemplaires'));

        if ($request->hasFile('fichier')) {
            $this->numerique->enregistrer($document, $request->file('fichier'));
        }

        return redirect()->route('admin.documents.show', $document)->with('succes', 'Document ajouté au catalogue.');
    }

    public function show(Document $document): View
    {
        $document = $this->documents->detail($document->id)
            ->load(['emprunts' => fn ($q) => $q->with(['inscription', 'exemplaire'])->latest()->limit(20)]);

        return view('admin.documents.show', compact('document'));
    }

    public function edit(Document $document, FiliereRepositoryInterface $filieres): View
    {
        return view('admin.documents.form', ['document' => $document, 'filieres' => $filieres->activesAvecSpecialites()]);
    }

    public function update(DocumentRequest $request, Document $document): RedirectResponse
    {
        $this->documents->update($document, $request->notice());

        if ($request->hasFile('fichier')) {
            $this->numerique->enregistrer($document, $request->file('fichier'));
        } elseif ($request->validated('supprimer_fichier')) {
            $this->numerique->supprimer($document);
        }

        return redirect()->route('admin.documents.show', $document)->with('succes', 'Notice mise à jour.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        if ($document->emprunts()->exists()) {
            return back()->withErrors(['document' => 'Ce document a un historique d\'emprunts : passez plutôt ses exemplaires « hors prêt ».']);
        }

        $this->numerique->supprimer($document);
        $this->documents->delete($document);

        return redirect()->route('admin.documents.index')->with('succes', "« {$document->titre} » a été retiré du catalogue.");
    }

    public function ajouterExemplaires(Request $request, Document $document): RedirectResponse
    {
        $nombre = (int) $request->validate(['nombre' => ['required', 'integer', 'min:1', 'max:50']])['nombre'];
        $this->documents->ajouterExemplaires($document, $nombre);

        return back()->with('succes', "{$nombre} exemplaire(s) ajouté(s).");
    }

    /** Met un exemplaire en rayon ou hors prêt (perdu, abîmé, en réparation). */
    public function etatExemplaire(Request $request, Exemplaire $exemplaire): RedirectResponse
    {
        $etat = EtatExemplaire::from($request->validate([
            'etat' => ['required', Rule::in([EtatExemplaire::Disponible->value, EtatExemplaire::Indisponible->value])],
        ])['etat']);

        if (in_array($exemplaire->etat, [EtatExemplaire::Reserve, EtatExemplaire::Emprunte], true)) {
            return back()->withErrors(['exemplaire' => "L'exemplaire {$exemplaire->code} est {$exemplaire->etat->libelle()} : enregistrez d'abord son retour."]);
        }

        $exemplaire->update(['etat' => $etat]);

        return back()->with('succes', "Exemplaire {$exemplaire->code} : {$etat->libelle()}.");
    }

    public function supprimerExemplaire(Exemplaire $exemplaire): RedirectResponse
    {
        if ($exemplaire->emprunts()->exists() || $exemplaire->etat !== EtatExemplaire::Indisponible) {
            return back()->withErrors(['exemplaire' => 'Seul un exemplaire hors prêt et jamais emprunté peut être supprimé.']);
        }

        $exemplaire->delete();

        return back()->with('succes', "Exemplaire {$exemplaire->code} supprimé.");
    }
}
