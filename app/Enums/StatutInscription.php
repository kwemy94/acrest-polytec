<?php

namespace App\Enums;

enum StatutInscription: string
{
    case EnAttente = 'en_attente';
    case Validee = 'validee';
    case Rejetee = 'rejetee';

    public function libelle(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente',
            self::Validee => 'Validée',
            self::Rejetee => 'Rejetée',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::EnAttente => 'warning',
            self::Validee => 'success',
            self::Rejetee => 'danger',
        };
    }
}
