<?php

namespace App\Enums;

enum EtatExemplaire: string
{
    case Disponible = 'disponible';
    case Reserve = 'reserve';
    case Emprunte = 'emprunte';
    case Indisponible = 'indisponible';

    public function libelle(): string
    {
        return match ($this) {
            self::Disponible => 'Disponible',
            self::Reserve => 'Réservé',
            self::Emprunte => 'Emprunté',
            self::Indisponible => 'Hors prêt',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::Disponible => 'success',
            self::Reserve => 'info',
            self::Emprunte => 'warning',
            self::Indisponible => 'neutre',
        };
    }
}
