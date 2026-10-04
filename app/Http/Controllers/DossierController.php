<?php

namespace App\Http\Controllers;

use App\Http\Requests\RechercheDossierRequest;
use App\Http\Requests\RetrouverCodeRequest;
use App\Repositories\Contracts\InscriptionRepositoryInterface;
use App\Services\Inscription\InscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Suivi de dossier par le candidat : reçu, statut, paiement. */
class DossierController extends Controller
{
    public function __construct(private readonly InscriptionRepositoryInterface $inscriptions)
    {
    }

    public function recherche(): View
    {
        return view('dossier.recherche');
    }

    public function rechercher(RechercheDossierRequest $request): RedirectResponse
    {
        $inscription = $this->inscriptions->findByCode($request->validated('code'));

        if (! $inscription || $inscription->email !== $request->validated('email')) {
            return back()->withInput()->withErrors([
                'code' => 'Aucun dossier ne correspond à ce code et à cette adresse e-mail.',
            ]);
        }

        $request->session()->push('dossiers', $inscription->code);

        return redirect()->route('dossier.show', $inscription->code);
    }

    public function show(Request $request, string $code): View|RedirectResponse
    {
        if (! in_array(strtoupper($code), (array) $request->session()->get('dossiers', []), true)) {
            return redirect()->route('dossier.recherche')->withInput(['code' => $code]);
        }

        $inscription = $this->inscriptions->findByCode($code) ?? abort(404);

        return view('dossier.show', compact('inscription'));
    }

    public function retrouver(): View
    {
        return view('dossier.retrouver');
    }

    public function envoyerCode(RetrouverCodeRequest $request, InscriptionService $service): RedirectResponse
    {
        $service->renvoyerCode($request->validated('email'), $request->validated('cni'));

        // Même réponse que le dossier existe ou non : on ne révèle aucune information.
        return back()->with('succes', 'Si un dossier correspond à ces informations, votre code vient d\'être envoyé à cette adresse e-mail. Pensez à vérifier vos courriers indésirables.');
    }
}
