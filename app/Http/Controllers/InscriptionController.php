<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inscription\CoordonneesRequest;
use App\Http\Requests\Inscription\DiplomeRequest;
use App\Http\Requests\Inscription\FinalisationRequest;
use App\Http\Requests\Inscription\FormationRequest;
use App\Http\Requests\Inscription\IdentiteRequest;
use App\Repositories\Contracts\InscriptionRepositoryInterface;
use App\Repositories\Contracts\SpecialiteRepositoryInterface;
use App\Services\Inscription\Etape;
use App\Services\Inscription\InscriptionService;
use App\Services\Inscription\InscriptionWizard;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Formulaire d'inscription en cinq étapes.
 * Chaque étape est validée côté serveur puis conservée en session jusqu'à l'envoi final.
 */
class InscriptionController extends Controller
{
    public function __construct(
        private readonly SpecialiteRepositoryInterface $specialites,
        private readonly InscriptionService $service,
    ) {
    }

    public function debut(InscriptionWizard $wizard): RedirectResponse
    {
        return redirect()->route('inscription.etape', $wizard->premiereIncomplete()->value);
    }

    public function afficher(Etape $etape, InscriptionWizard $wizard): View|RedirectResponse
    {
        if (! $wizard->estAccessible($etape)) {
            return redirect()
                ->route('inscription.etape', $wizard->premiereIncomplete()->value)
                ->with('info', 'Terminez d\'abord cette étape pour continuer.');
        }

        $catalogue = $this->specialites->catalogue();

        return view('inscription.etape', [
            'etape' => $etape,
            'etapes' => Etape::cases(),
            'wizard' => $wizard,
            'valeurs' => $wizard->donnees($etape),
            'donnees' => $wizard->toutes(),
            'catalogue' => $catalogue,
            'nomsSpecialites' => $this->nomsSpecialites($catalogue),
        ]);
    }

    public function identite(IdentiteRequest $request, InscriptionWizard $wizard): RedirectResponse
    {
        return $this->suivante(Etape::Identite, $request->validated(), $wizard);
    }

    public function coordonnees(CoordonneesRequest $request, InscriptionWizard $wizard): RedirectResponse
    {
        return $this->suivante(Etape::Coordonnees, $request->validated(), $wizard);
    }

    public function diplome(DiplomeRequest $request, InscriptionWizard $wizard): RedirectResponse
    {
        return $this->suivante(Etape::Diplome, $request->validated(), $wizard);
    }

    public function formation(FormationRequest $request, InscriptionWizard $wizard): RedirectResponse
    {
        return $this->suivante(Etape::Formation, ['choix' => $request->choix()], $wizard);
    }

    public function finaliser(FinalisationRequest $request, InscriptionWizard $wizard): RedirectResponse
    {
        if (! $wizard->estPret()) {
            return redirect()->route('inscription.etape', $wizard->premiereIncomplete()->value);
        }

        $inscription = $this->service->finaliser($wizard->toutes());
        $wizard->vider();

        $request->session()->push('dossiers', $inscription->code);

        return redirect()
            ->route('dossier.show', $inscription->code)
            ->with('nouvelle_inscription', true);
    }

    public function recommencer(InscriptionWizard $wizard): RedirectResponse
    {
        $wizard->vider();

        return redirect()->route('inscription.etape', Etape::Identite->value);
    }

    /** Index id => [nom, filière], en conservant les ids comme clés. */
    private function nomsSpecialites(array $catalogue): array
    {
        $noms = [];
        foreach ($catalogue as $filiere) {
            foreach ($filiere['specialites'] as $specialite) {
                $noms[$specialite['id']] = ['nom' => $specialite['nom'], 'filiere' => $filiere['nom']];
            }
        }

        return $noms;
    }

    private function suivante(Etape $etape, array $donnees, InscriptionWizard $wizard): RedirectResponse
    {
        $wizard->enregistrer($etape, $donnees);

        // Si le candidat revient corriger une étape, on le ramène à la vérification.
        $cible = request()->boolean('retour_verification') && $wizard->estPret()
            ? Etape::Verification
            : $etape->suivante();

        return redirect()->route('inscription.etape', $cible->value);
    }
}
