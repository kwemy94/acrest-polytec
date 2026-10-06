<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\InscriptionRepositoryInterface;
use App\Repositories\Contracts\NewsletterRepositoryInterface;
use App\Repositories\Contracts\PaiementRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(
        Request $request,
        InscriptionRepositoryInterface $inscriptions,
        PaiementRepositoryInterface $paiements,
        NewsletterRepositoryInterface $newsletter,
    ): View|RedirectResponse {
        // Le bibliothécaire n'a accès qu'à la bibliothèque.
        if (! $request->user()->estAdmin()) {
            return redirect()->route('admin.bibliotheque');
        }

        $parStatut = $inscriptions->compterParStatut();

        return view('admin.dashboard', [
            'parStatut' => $parStatut,
            'total' => array_sum($parStatut),
            'encaisse' => $paiements->totalEncaisse(),
            'paiementsEnAttente' => $paiements->compterEnAttente(),
            'abonnes' => $newsletter->count(),
            'parFiliere' => $inscriptions->premiersChoixParFiliere(),
            'dernieres' => $inscriptions->dernieres(6),
        ]);
    }
}
