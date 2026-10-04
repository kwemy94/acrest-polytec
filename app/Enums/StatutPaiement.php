<?php

namespace App\Enums;

enum StatutPaiement: string
{
    case EnAttente = 'en_attente';
    case Valide = 'valide';
    case Rejete = 'rejete';

    public function libelle(): string
    {
        return match ($this) {
            self::EnAttente => 'En cours de vérification',
            self::Valide => 'Confirmé',
            self::Rejete => 'Rejeté',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::EnAttente => 'warning',
            self::Valide => 'success',
            self::Rejete => 'danger',
        };
    }
}
