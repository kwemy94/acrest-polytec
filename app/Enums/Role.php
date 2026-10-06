<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Bibliothecaire = 'bibliothecaire';

    public function libelle(): string
    {
        return match ($this) {
            self::Admin => 'Administrateur',
            self::Bibliothecaire => 'Bibliothécaire',
        };
    }
}
