<?php

namespace App\Http\Controllers\Admin\Bibliotheque;

use App\Enums\EtatPhysique;
use App\Enums\NiveauAcces;
use App\Enums\StatutExemplaire;
use App\Exceptions\BibliothequeException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Bibliotheque\DocumentRequest;
use App\Models\Categorie;
use App\Models\Document;
use App\Models\Emprunt;
use App\Models\Exemplaire;
use App\Models\JournalActivite;
use App\Models\RessourceNumerique;
use App\Models\TypeDocument;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use App\Services\Bibliotheque\CatalogueService;
use App\Services\Bibliotheque\PlanBibliotheque;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentRepositoryInterface $documents,
        private readonly CatalogueService $catalogue,
        private readonly PlanBibliotheque $plan,
    ) {
    }

    private function referentiels(): array
    {
        return [
            'types' => TypeDocument::actifs()->get(),
            'categories' => Categorie::orderBy('nom')->get(),
            'localisations' => $this->plan->options(),
        ];
    }

    public function index(Request $request): View
    {
        $filtres = $request->only(['q', 'auteur', 'mot_cle', 'isbn', 'type', 'categorie', 'langue', 'localisation', 'disponible', 'numerique']);

        return view('admin.bibliotheque.documents.index', [
            'documents' => $this->documents->rechercher($filtres, 20),
            'filtres' => $filtres,
            ...$this->referentiels(),
        ]);
    }

    public function create(): View
    {
        return view('admin.bibliotheque.documents.form', ['document' => new Document(), ...$this->referentiels()]);
    }

    public function store(DocumentRequest $request): RedirectResponse
    {
        $document = DB::transaction(function () use ($request) {
            $document = $this->catalogue->creerDocument($request->notice(), $request->auteurs());
            $nombre = (int) $request->validated('exemplaires');
            if ($nombre > 0) {
                $this->catalogue->ajouterExemplaires($document, $nombre, $request->exemplaire());
            }

            return $document;
        });

        return redirect()->route('admin.documents.show', $document)->with('succes', 'Document ajouté au catalogue.');
    }

    public function show(Document $document): View
    {
        $document = $this->documents->detail($document->id);

        // Historique du document : la notice, ses exemplaires, ses ressources et ses prêts.
        $sujets = [
            [$document, [$document->id]],
            [new Exemplaire(), $document->exemplaires->pluck('id')],
            [new RessourceNumerique(), $document->ressources->pluck('id')],
            [new Emprunt(), $document->emprunts()->pluck('id')],
        ];

        return view('admin.bibliotheque.documents.show', [
            'document' => $document,
            'emprunts' => $document->emprunts()->with(['adherent', 'exemplaire'])->latest()->limit(15)->get(),
            'historique' => JournalActivite::with(['user', 'adherent'])
                ->where(function (Builder $q) use ($sujets) {
                    foreach ($sujets as [$modele, $ids]) {
                        $q->orWhere(fn (Builder $w) => $w->where('sujet_type', $modele->getMorphClass())->whereIn('sujet_id', $ids));
                    }
                })
                ->latest('id')
                ->limit(25)
                ->get(),
            'localisations' => $this->plan->options(),
            'etats' => EtatPhysique::cases(),
            'niveaux' => NiveauAcces::cases(),
            'statutsManuels' => StatutExemplaire::manuels(),
        ]);
    }

    public function edit(Document $document): View
    {
        return view('admin.bibliotheque.documents.form', ['document' => $document->load('auteurs'), ...$this->referentiels()]);
    }

    public function update(DocumentRequest $request, Document $document): RedirectResponse
    {
        $this->catalogue->modifierDocument($document, $request->notice(), $request->auteurs());

        return redirect()->route('admin.documents.show', $document)->with('succes', 'Notice mise à jour.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        try {
            $this->catalogue->supprimerDocument($document);
        } catch (BibliothequeException $e) {
            return back()->withErrors(['document' => $e->getMessage()]);
        }

        return redirect()->route('admin.documents.index')->with('succes', "« {$document->titre} » a été retiré du catalogue.");
    }
}
