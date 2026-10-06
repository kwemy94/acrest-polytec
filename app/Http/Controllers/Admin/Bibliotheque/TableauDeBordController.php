<?php

namespace App\Http\Controllers\Admin\Bibliotheque;

use App\Enums\StatutEmprunt;
use App\Http\Controllers\Controller;
use App\Models\Emprunt;
use App\Models\JournalActivite;
use App\Repositories\Contracts\AdherentRepositoryInterface;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use App\Repositories\Contracts\EmpruntRepositoryInterface;
use Illuminate\View\View;

class TableauDeBordController extends Controller
{
    public function __invoke(
        DocumentRepositoryInterface $documents,
        EmpruntRepositoryInterface $emprunts,
        AdherentRepositoryInterface $adherents,
    ): View {
        return view('admin.bibliotheque.tableau-de-bord', [
            'stats' => $documents->statistiques(),
            'circulation' => $emprunts->compteurs(),
            'adherentsActifs' => $adherents->compterActifs(),
            'retards' => Emprunt::with(['adherent', 'document', 'exemplaire'])
                ->where('statut', StatutEmprunt::EnCours->value)
                ->whereDate('date_retour_prevue', '<', today())
                ->orderBy('date_retour_prevue')
                ->limit(8)
                ->get(),
            'activites' => JournalActivite::with(['user', 'adherent'])->latest('id')->limit(10)->get(),
        ]);
    }
}
