<?php

namespace App\Mail;

use App\Models\Emprunt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Alerte la bibliothèque qu'une demande d'emprunt attend d'être traitée. */
class NouvelleDemandeEmprunt extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Emprunt $emprunt)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Nouvelle demande d\'emprunt : '.$this->emprunt->document->titre);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.nouvelle-demande-emprunt');
    }
}
