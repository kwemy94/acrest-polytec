<?php

namespace App\Mail;

use App\Enums\EvenementEmprunt;
use App\Models\Emprunt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** E-mail envoyé à l'étudiant à chaque étape de son emprunt. */
class EmpruntNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Emprunt $emprunt, public EvenementEmprunt $evenement)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Bibliothèque ACREST — '.$this->evenement->sujet());
    }

    public function content(): Content
    {
        return new Content(view: 'mail.emprunt');
    }
}
