<?php

namespace App\Enums;

enum TypeIncident: string
{
    case Perte = 'perte';
    case Dommage = 'dommage';
    case Retard = 'retard';
    case Autre = 'autre';

    public function libelle(): string
    {
        return match ($this) {
            self::Perte => 'Perte',
            self::Dommage => 'Dommage',
            self::Retard => 'Retour en retard',
            self::Autre => 'Autre',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::Perte, self::Dommage => 'danger',
            self::Retard => 'warning',
            self::Autre => 'neutre',
        };
    }
}
