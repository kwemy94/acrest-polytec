<?php

namespace App\Http\Controllers\Admin\Bibliotheque;

use App\Enums\NiveauAcces;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Bibliotheque\RessourceRequest;
use App\Models\Document;
use App\Models\RessourceNumerique;
use App\Services\Bibliotheque\BibliothequeNumerique;
use Illuminate\Http\RedirectResponse;

/** Ressources numériques d'un document : ajout, droits d'accès, suppression. */
class RessourceController extends Controller
{
    public function __construct(private readonly BibliothequeNumerique $numerique)
    {
    }

    public function store(RessourceRequest $request, Document $document): RedirectResponse
    {
        $ressource = $this->numerique->ajouter(
            $document,
            $request->file('fichier'),
            NiveauAcces::from($request->validated('niveau_acces')),
            $request->validated('version'),
            $request->validated('titre'),
        );

        return back()->with('succes', "Ressource « {$ressource->libelle} » ajoutée ({$ressource->niveau_acces->libelle()}).");
    }

    public function update(RessourceRequest $request, RessourceNumerique $ressource): RedirectResponse
    {
        $this->numerique->modifier($ressource, [
            'titre' => $request->validated('titre'),
            'version' => $request->validated('version') ?: $ressource->version,
            'niveau_acces' => $request->validated('niveau_acces'),
        ]);

        return back()->with('succes', "Ressource « {$ressource->libelle} » : {$ressource->niveau_acces->libelle()}.");
    }

    public function destroy(RessourceNumerique $ressource): RedirectResponse
    {
        $this->numerique->supprimer($ressource);

        return back()->with('succes', 'Ressource numérique supprimée.');
    }
}
