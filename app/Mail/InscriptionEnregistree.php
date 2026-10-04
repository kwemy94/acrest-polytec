<?php

namespace App\Mail;

use App\Models\Inscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InscriptionEnregistree extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Inscription $inscription)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Votre code d\'inscription ACREST Polytechnique : '.$this->inscription->code);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.inscription-enregistree');
    }
}
