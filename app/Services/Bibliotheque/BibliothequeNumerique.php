<?php

namespace App\Services\Bibliotheque;

use App\Models\Document;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Versions PDF des documents. Les fichiers sont rangés sur le disque privé :
 * ils ne sont jamais accessibles par une URL directe, seulement via l'application.
 */
class BibliothequeNumerique
{
    private const DOSSIER = 'bibliotheque';

    private function disque(): Filesystem
    {
        return Storage::disk('local');
    }

    /** Enregistre (ou remplace) le PDF d'un document. */
    public function enregistrer(Document $document, UploadedFile $pdf): void
    {
        $ancien = $document->fichier;
        $chemin = $pdf->storeAs(self::DOSSIER, $document->id.'-'.Str::random(12).'.pdf', 'local');

        $document->update(['fichier' => $chemin, 'fichier_taille' => $pdf->getSize()]);

        if ($ancien && $ancien !== $chemin) {
            $this->disque()->delete($ancien);
        }
    }

    public function supprimer(Document $document): void
    {
        if ($document->fichier) {
            $this->disque()->delete($document->fichier);
        }

        $document->update(['fichier' => null, 'fichier_taille' => null, 'telechargeable' => false]);
    }

    public function existe(Document $document): bool
    {
        return $document->fichier && $this->disque()->exists($document->fichier);
    }

    /** Affichage dans le navigateur (lecteur PDF intégré). */
    public function afficher(Document $document): StreamedResponse
    {
        return $this->disque()->response($document->fichier, $this->nomFichier($document), [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ], 'inline');
    }

    public function telecharger(Document $document): StreamedResponse
    {
        return $this->disque()->download($document->fichier, $this->nomFichier($document), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function nomFichier(Document $document): string
    {
        return (Str::slug(Str::limit($document->titre, 80, '')) ?: 'document').'.pdf';
    }
}
