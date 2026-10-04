<?php

namespace App\Services\Bibliotheque;

use App\Enums\EvenementEmprunt;
use App\Mail\EmpruntNotification;
use App\Mail\NouvelleDemandeEmprunt;
use App\Models\Emprunt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/** Envoi des e-mails de la bibliothèque. Un échec d'envoi ne bloque jamais l'opération. */
class NotificateurBibliotheque
{
    public function etudiant(Emprunt $emprunt, EvenementEmprunt $evenement): bool
    {
        $email = $emprunt->inscription?->email;

        if (! $email) {
            return false;
        }

        return $this->envoyer($email, new EmpruntNotification($emprunt, $evenement), $emprunt);
    }

    public function bibliotheque(Emprunt $emprunt): bool
    {
        $email = config('acrest.bibliotheque.email');

        return $email ? $this->envoyer($email, new NouvelleDemandeEmprunt($emprunt), $emprunt) : false;
    }

    private function envoyer(string $email, $mail, Emprunt $emprunt): bool
    {
        try {
            Mail::to($email)->send($mail);

            return true;
        } catch (Throwable $e) {
            Log::warning('Envoi d\'un mail de bibliothèque impossible', ['emprunt' => $emprunt->id, 'erreur' => $e->getMessage()]);

            return false;
        }
    }
}
