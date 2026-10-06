<?php

namespace App\Enums;

/** Droits sur une ressource numérique. */
enum NiveauAcces: string
{
    case Consultation = 'consultation';
    case Telechargement = 'telechargement';
    case ConsultationTelechargement = 'consultation_telechargement';
    case Restreint = 'restreint';

    public function libelle(): string
    {
        return match ($this) {
            self::Consultation => 'Consultation uniquement',
            self::Telechargement => 'Téléchargement autorisé',
            self::ConsultationTelechargement => 'Consultation et téléchargement',
            self::Restreint => 'Accès restreint (personnel)',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::Consultation => 'info',
            self::Telechargement, self::ConsultationTelechargement => 'success',
            self::Restreint => 'danger',
        };
    }

    public function permetConsultation(): bool
    {
        return in_array($this, [self::Consultation, self::ConsultationTelechargement], true);
    }

    public function permetTelechargement(): bool
    {
        return in_array($this, [self::Telechargement, self::ConsultationTelechargement], true);
    }
}
