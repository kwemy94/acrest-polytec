<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\InscriptionRepositoryInterface;
use App\Repositories\Contracts\NewsletterRepositoryInterface;
use App\Repositories\Contracts\PaiementRepositoryInterface;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(
        InscriptionRepositoryInterface $inscriptions,
        PaiementRepositoryInterface $paiements,
        NewsletterRepositoryInterface $newsletter,
    ): View {
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
