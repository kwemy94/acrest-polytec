<?php

namespace App\Enums;

enum EtatPhysique: string
{
    case Neuf = 'neuf';
    case Bon = 'bon';
    case Moyen = 'moyen';
    case Endommage = 'endommage';

    public function libelle(): string
    {
        return match ($this) {
            self::Neuf => 'Neuf',
            self::Bon => 'Bon',
            self::Moyen => 'Moyen',
            self::Endommage => 'Endommagé',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::Neuf, self::Bon => 'success',
            self::Moyen => 'warning',
            self::Endommage => 'danger',
        };
    }
}
