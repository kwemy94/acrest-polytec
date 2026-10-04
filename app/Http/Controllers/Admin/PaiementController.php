<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OperateurPaiement;
use App\Enums\StatutPaiement;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TraitementPaiementRequest;
use App\Models\Paiement;
use App\Repositories\Contracts\PaiementRepositoryInterface;
use App\Services\Paiement\PaiementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaiementController extends Controller
{
    public function index(Request $request, PaiementRepositoryInterface $paiements): View
    {
        $filtres = $request->only(['q', 'statut', 'operateur']);

        return view('admin.paiements.index', [
            'paiements' => $paiements->rechercher($filtres),
            'filtres' => $filtres,
            'statuts' => StatutPaiement::cases(),
            'operateurs' => OperateurPaiement::cases(),
            'encaisse' => $paiements->totalEncaisse(),
        ]);
    }

    public function traiter(TraitementPaiementRequest $request, Paiement $paiement, PaiementService $service): RedirectResponse
    {
        if ($paiement->statut !== StatutPaiement::EnAttente) {
            return back()->withErrors(['paiement' => 'Ce paiement a déjà été traité.']);
        }

        $note = $request->validated('note');
        $request->validated('decision') === 'valider'
            ? $service->valider($paiement, $request->user()->id, $note)
            : $service->rejeter($paiement, $request->user()->id, $note);

        return back()->with('succes', "Paiement {$paiement->reference} : {$paiement->statut->libelle()}.");
    }
}
