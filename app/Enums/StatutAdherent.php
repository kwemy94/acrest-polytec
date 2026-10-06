<?php

namespace App\Enums;

enum StatutAdherent: string
{
    case Actif = 'actif';
    case Suspendu = 'suspendu';
    case Expire = 'expire';

    public function libelle(): string
    {
        return match ($this) {
            self::Actif => 'Actif',
            self::Suspendu => 'Suspendu',
            self::Expire => 'Expiré',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::Actif => 'success',
            self::Suspendu => 'danger',
            self::Expire => 'neutre',
        };
    }
}
