<?php

namespace App\Services\Bibliotheque;

use App\Models\Inscription;

/** Étudiant connecté à l'espace bibliothèque (identifié par son code d'inscription et son e-mail). */
class SessionLecteur
{
    private const CLE = 'bibliotheque.lecteur';

    public function courant(): ?Inscription
    {
        $request = request();

        if ($request->attributes->has(self::CLE) || ! $request->hasSession()) {
            return $request->attributes->get(self::CLE);
        }

        $id = $request->session()->get(self::CLE);
        $inscription = $id ? Inscription::find($id) : null;

        // Un dossier repassé en attente ou rejeté perd l'accès au prêt.
        if ($inscription && ! $inscription->peutEmprunter()) {
            $this->deconnecter();
            $inscription = null;
        }

        $request->attributes->set(self::CLE, $inscription);

        return $inscription;
    }

    public function connecter(Inscription $inscription): void
    {
        request()->session()->regenerate();
        request()->session()->put(self::CLE, $inscription->id);
        request()->attributes->set(self::CLE, $inscription);
    }

    public function deconnecter(): void
    {
        request()->session()->forget(self::CLE);
        request()->attributes->remove(self::CLE);
    }
}
