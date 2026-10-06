<?php

namespace App\Services\Bibliotheque;

use App\Models\Adherent;

/** Adhérent connecté à l'espace bibliothèque (identifié par son matricule et son e-mail). */
class SessionLecteur
{
    private const CLE = 'bibliotheque.adherent';

    public function courant(): ?Adherent
    {
        $request = request();

        if ($request->attributes->has(self::CLE) || ! $request->hasSession()) {
            return $request->attributes->get(self::CLE);
        }

        $id = $request->session()->get(self::CLE);
        $adherent = $id ? Adherent::find($id) : null;

        if ($id && ! $adherent) {
            $this->deconnecter();
        }

        $request->attributes->set(self::CLE, $adherent);

        return $adherent;
    }

    public function connecter(Adherent $adherent): void
    {
        request()->session()->regenerate();
        request()->session()->put(self::CLE, $adherent->id);
        request()->attributes->set(self::CLE, $adherent);
    }

    public function deconnecter(): void
    {
        request()->session()->forget(self::CLE);
        request()->attributes->remove(self::CLE);
    }
}
