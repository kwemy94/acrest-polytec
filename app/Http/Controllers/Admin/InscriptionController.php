<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatutInscription;
use App\Http\Controllers\Controller;
use App\Models\Inscription;
use App\Repositories\Contracts\FiliereRepositoryInterface;
use App\Repositories\Contracts\InscriptionRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InscriptionController extends Controller
{
    public function __construct(private readonly InscriptionRepositoryInterface $inscriptions)
    {
    }

    private function filtres(Request $request): array
    {
        return $request->only(['q', 'filiere', 'specialite', 'statut', 'paiement']);
    }

    public function index(Request $request, FiliereRepositoryInterface $filieres): View
    {
        return view('admin.inscriptions.index', [
            'inscriptions' => $this->inscriptions->rechercher($this->filtres($request)),
            'filieres' => $filieres->activesAvecSpecialites(),
            'filtres' => $this->filtres($request),
        ]);
    }

    public function show(Inscription $inscription): View
    {
        $inscription->load(['specialites.filiere', 'paiements.agent']);

        return view('admin.inscriptions.show', compact('inscription'));
    }

    public function statut(Request $request, Inscription $inscription): RedirectResponse
    {
        $data = $request->validate(['statut' => ['required', Rule::enum(StatutInscription::class)]]);
        $statut = StatutInscription::from($data['statut']);

        $this->inscriptions->changerStatut($inscription, $statut);

        return back()->with('succes', "Dossier {$inscription->code} : {$statut->libelle()}.");
    }

    public function destroy(Inscription $inscription): RedirectResponse
    {
        $this->inscriptions->delete($inscription);

        return redirect()->route('admin.inscriptions.index')->with('succes', "Dossier {$inscription->code} supprimé.");
    }

    /** Export CSV (séparateur « ; » et BOM UTF-8 pour une ouverture directe dans Excel). */
    public function export(Request $request): StreamedResponse
    {
        $lignes = $this->inscriptions->exporter($this->filtres($request));

        return response()->streamDownload(function () use ($lignes) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Code', 'Nom', 'Prénom', 'Sexe', 'Date de naissance', 'Lieu', 'Pays', 'CNI', 'Téléphone', 'E-mail',
                'Père', 'Mère', 'Contact parent', 'Diplôme', 'Série/option', 'Année', 'Choix 1', 'Choix 2', 'Choix 3',
                'Statut', 'Paiement', 'Date d\'inscription'], ';');

            foreach ($lignes as $i) {
                $choix = $i->specialites->pluck('nom')->pad(3, '')->all();
                fputcsv($out, [
                    $i->code, $i->nom, $i->prenom, $i->sexe?->libelle(), $i->date_naissance?->format('d/m/Y'),
                    $i->lieu_naissance, $i->pays, $i->cni, $i->telephone, $i->email, $i->nom_pere, $i->nom_mere,
                    $i->contact_parent, $i->diplome, $i->option_diplome, $i->annee_obtention, ...$choix,
                    $i->statut->libelle(), $i->estPayee() ? 'Payé' : 'Non payé', $i->created_at->format('d/m/Y H:i'),
                ], ';');
            }
            fclose($out);
        }, 'inscriptions-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
