<?php

namespace App\Http\Controllers\Admin\Bibliotheque;

use App\Enums\StatutAdherent;
use App\Enums\TypeAdherent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Bibliotheque\AdherentRequest;
use App\Models\Adherent;
use App\Models\Inscription;
use App\Repositories\Contracts\AdherentRepositoryInterface;
use App\Repositories\Contracts\EmpruntRepositoryInterface;
use App\Services\Bibliotheque\AdherentService;
use App\Services\Bibliotheque\ParametresPret;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdherentController extends Controller
{
    public function __construct(private readonly AdherentService $service)
    {
    }

    public function index(Request $request, AdherentRepositoryInterface $adherents): View
    {
        $filtres = $request->only(['q', 'type', 'statut']);

        return view('admin.bibliotheque.adherents.index', [
            'adherents' => $adherents->rechercher($filtres),
            'filtres' => $filtres,
            'types' => TypeAdherent::cases(),
            'statuts' => StatutAdherent::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin.bibliotheque.adherents.form', [
            'adherent' => new Adherent([
                'type' => TypeAdherent::Etudiant,
                'statut' => StatutAdherent::Actif,
                'date_inscription' => today(),
                'date_expiration' => today()->addYear(),
            ]),
            // Étudiants en règle (dossier validé, frais payés) sans fiche adhérent
            'etudiants' => Inscription::enRegle()->sansFicheAdherent()
                ->orderBy('nom')->orderBy('prenom')
                ->get(['id', 'code', 'nom', 'prenom', 'email', 'telephone']),
        ]);
    }

    public function store(AdherentRequest $request): RedirectResponse
    {
        $adherent = $this->service->creer($request->validated());

        return redirect()->route('admin.adherents.show', $adherent)->with('succes', 'Adhérent enregistré.');
    }

    public function show(Adherent $adherent, EmpruntRepositoryInterface $emprunts, ParametresPret $parametres): View
    {
        return view('admin.bibliotheque.adherents.show', [
            'adherent' => $adherent,
            'emprunts' => $emprunts->deAdherent($adherent),
            'actifs' => $emprunts->compterActifs($adherent),
            'quota' => $parametres->maxPrets($adherent->type),
            'duree' => $parametres->dureePret($adherent->type),
        ]);
    }

    public function edit(Adherent $adherent): View
    {
        return view('admin.bibliotheque.adherents.form', compact('adherent'));
    }

    public function update(AdherentRequest $request, Adherent $adherent): RedirectResponse
    {
        $this->service->modifier($adherent, $request->validated());

        return redirect()->route('admin.adherents.show', $adherent)->with('succes', 'Fiche adhérent mise à jour.');
    }

    /** Crée les fiches des étudiants dont le dossier d'inscription est validé. */
    public function importer(Request $request): RedirectResponse
    {
        $date = $request->validate(['date_expiration' => ['nullable', 'date', 'after:today']])['date_expiration'] ?? null;
        $nombre = $this->service->importerEtudiants($date);

        return back()->with($nombre ? 'succes' : 'info', $nombre
            ? "{$nombre} étudiant(s) ajouté(s) comme adhérents (matricule = code d'inscription)."
            : 'Aucun nouvel étudiant validé à importer.');
    }
}
