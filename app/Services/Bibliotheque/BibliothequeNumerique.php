<?php

namespace App\Services\Bibliotheque;

use App\Enums\NiveauAcces;
use App\Enums\TypeRessource;
use App\Models\Adherent;
use App\Models\Document;
use App\Models\RessourceNumerique;
use App\Models\User;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Ressources numériques des documents. Les fichiers sont rangés sur le disque privé :
 * aucune URL publique, chaque accès passe par l'application qui vérifie les droits.
 */
class BibliothequeNumerique
{
    private const DOSSIER = 'bibliotheque';

    public function __construct(private readonly Journal $journal)
    {
    }

    private function disque(): Filesystem
    {
        return Storage::disk('local');
    }

    public function ajouter(Document $document, UploadedFile $fichier, NiveauAcces $niveau, ?string $version = null, ?string $titre = null): RessourceNumerique
    {
        $extension = strtolower($fichier->getClientOriginalExtension() ?: $fichier->extension());
        $chemin = $fichier->storeAs(self::DOSSIER.'/'.$document->id, Str::random(24).'.'.$extension, 'local');

        $ressource = $document->ressources()->create([
            'titre' => $titre,
            'fichier' => $chemin,
            'nom_original' => Str::limit($fichier->getClientOriginalName(), 250, ''),
            'mime' => $fichier->getMimeType() ?: 'application/octet-stream',
            'type' => TypeRessource::depuisExtension($extension),
            'taille' => $fichier->getSize(),
            'version' => $version ?: '1',
            'niveau_acces' => $niveau,
            'ajoute_par' => auth()->id(),
        ]);

        $this->journal->enregistrer('ressource.ajout', $ressource,
            "Ajout de la ressource « {$ressource->libelle} » à « {$document->titre} »",
            ['fichier' => $ressource->nom_original, 'niveau_acces' => $niveau->value]);

        return $ressource;
    }

    public function modifier(RessourceNumerique $ressource, array $attributs): RessourceNumerique
    {
        $ressource->update($attributs);
        $this->journal->enregistrer('ressource.modification', $ressource,
            "Modification de la ressource « {$ressource->libelle} »",
            ['champs' => collect($ressource->getChanges())->except('updated_at')->keys()->all()]);

        return $ressource;
    }

    public function supprimer(RessourceNumerique $ressource): void
    {
        $this->disque()->delete($ressource->fichier);
        $this->journal->enregistrer('ressource.suppression', $ressource->document,
            "Suppression de la ressource « {$ressource->libelle} »", ['fichier' => $ressource->nom_original]);
        $ressource->delete();
    }

    /* ---------------------------------------------------------------
     | Droits d'accès
     * ------------------------------------------------------------- */

    /** Le personnel a toujours accès ; un adhérent actif selon le niveau de la ressource. */
    public function peutConsulter(RessourceNumerique $ressource, ?User $user, ?Adherent $adherent): bool
    {
        if (! $ressource->type->lisibleEnLigne()) {
            return false;
        }

        return $user !== null || ($adherent?->estActif() && $ressource->niveau_acces->permetConsultation());
    }

    public function peutTelecharger(RessourceNumerique $ressource, ?User $user, ?Adherent $adherent): bool
    {
        return $user !== null || ($adherent?->estActif() && $ressource->niveau_acces->permetTelechargement());
    }

    /* ---------------------------------------------------------------
     | Diffusion
     * ------------------------------------------------------------- */

    public function existe(RessourceNumerique $ressource): bool
    {
        return $this->disque()->exists($ressource->fichier);
    }

    /** Lecture dans le navigateur (PDF, audio, vidéo), avec prise en charge des requêtes partielles. */
    public function afficher(RessourceNumerique $ressource): BinaryFileResponse
    {
        $reponse = response()->file($this->disque()->path($ressource->fichier), [
            'Content-Type' => $ressource->mime,
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
        $nom = $this->nomFichier($ressource);

        return $reponse->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $nom, Str::ascii($nom));
    }

    public function telecharger(RessourceNumerique $ressource): BinaryFileResponse
    {
        return response()->download($this->disque()->path($ressource->fichier), $this->nomFichier($ressource), [
            'Content-Type' => $ressource->mime,
        ]);
    }

    private function nomFichier(RessourceNumerique $ressource): string
    {
        $base = Str::slug(Str::limit($ressource->document->titre, 80, '')) ?: 'document';
        $extension = pathinfo($ressource->fichier, PATHINFO_EXTENSION);

        return $base.($ressource->version !== '1' ? '-v'.Str::slug($ressource->version) : '').'.'.$extension;
    }
}
