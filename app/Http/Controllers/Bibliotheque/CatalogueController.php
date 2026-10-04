<?php

namespace App\Http\Controllers\Bibliotheque;

use App\Enums\TypeDocument;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use App\Repositories\Contracts\EmpruntRepositoryInterface;
use App\Repositories\Contracts\FiliereRepositoryInterface;
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

    public function index(Request $request, FiliereRepositoryInterface $filieres): View
    {
        $filtres = $request->only(['q', 'type', 'langue', 'filiere', 'disponible', 'numerique']);

        return view('bibliotheque.index', [
            'documents' => $this->documents->rechercher($filtres),
            'filtres' => $filtres,
            'types' => TypeDocument::cases(),
            'filieres' => $filieres->activesAvecSpecialites(),
            'lecteur' => $this->session->courant(),
        ]);
    }

    public function show(Document $document, EmpruntRepositoryInterface $emprunts): View
    {
        $lecteur = $this->session->courant();
        $document = $this->documents->detail($document->id);

        return view('bibliotheque.show', [
            'document' => $document,
            'lecteur' => $lecteur,
            'enAttente' => $emprunts->compterDemandes($document),
            'dejaDemande' => $lecteur && $emprunts->demandeActive($lecteur, $document),
        ]);
    }
}
