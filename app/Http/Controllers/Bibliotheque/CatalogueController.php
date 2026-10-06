<?php

namespace App\Http\Controllers\Bibliotheque;

use App\Enums\NiveauAcces;
use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Document;
use App\Models\TypeDocument;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use App\Repositories\Contracts\EmpruntRepositoryInterface;
use App\Services\Bibliotheque\BibliothequeNumerique;
use App\Services\Bibliotheque\EmpruntService;
use App\Services\Bibliotheque\SessionLecteur;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Catalogue public de la bibliothèque. */
class CatalogueController extends Controller
{
    public function __construct(
        private readonly DocumentRepositoryInterface $documents,
        private readonly SessionLecteur $session,
    ) {
    }

    public function index(Request $request): View
    {
        $filtres = $request->only(['q', 'auteur', 'mot_cle', 'type', 'categorie', 'langue', 'disponible', 'numerique']);

        return view('bibliotheque.index', [
            'documents' => $this->documents->rechercher($filtres),
            'filtres' => $filtres,
            'types' => TypeDocument::actifs()->get(),
            'categories' => Categorie::orderBy('nom')->get(),
            'lecteur' => $this->session->courant(),
        ]);
    }

    public function show(Document $document, EmpruntRepositoryInterface $emprunts, EmpruntService $service, BibliothequeNumerique $numerique): View
    {
        $lecteur = $this->session->courant();
        $document = $this->documents->detail($document->id);
        $personnel = auth()->user();

        return view('bibliotheque.show', [
            'document' => $document,
            'lecteur' => $lecteur,
            'enAttente' => $emprunts->compterDemandes($document),
            'demandeEnCours' => $lecteur ? $emprunts->actifPourDocument($lecteur, $document) : null,
            'demandable' => $service->demandable($document),
            // Le personnel voit aussi les ressources à accès restreint.
            'ressources' => $document->ressources
                ->filter(fn ($r) => $personnel || $r->niveau_acces !== NiveauAcces::Restreint)
                ->map(fn ($r) => [
                    'ressource' => $r,
                    'consulter' => $numerique->peutConsulter($r, $personnel, $lecteur),
                    'telecharger' => $numerique->peutTelecharger($r, $personnel, $lecteur),
                ]),
        ]);
    }
}
