<?php

namespace App\Http\Controllers;

use App\Enums\OperateurPaiement;
use App\Exceptions\PaiementException;
use App\Http\Requests\PaiementRequest;
use App\Services\Paiement\PaiementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaiementController extends Controller
{
    public function __construct(private readonly PaiementService $service)
    {
    }

    public function create(Request $request): View
    {
        return view('paiement.create', [
            'montant' => $this->service->montant(),
            'operateurs' => OperateurPaiement::cases(),
            'code' => $request->query('code'),
        ]);
    }

    public function store(PaiementRequest $request): RedirectResponse
    {
        try {
            $paiement = $this->service->declarer($request->validated());
        } catch (PaiementException $e) {
            return back()->withInput()->withErrors(['paiement' => $e->getMessage()]);
        }

        $code = $paiement->inscription->code;
        $request->session()->push('dossiers', $code);

        return redirect()
            ->route('dossier.show', $code)
            ->with('succes', 'Paiement déclaré. Il sera confirmé après vérification par la scolarité, généralement sous 48 heures.');
    }
}
