<?php

namespace App\Enums;

enum OperateurPaiement: string
{
    case MtnMomo = 'mtn_momo';
    case OrangeMoney = 'orange_money';

    public function libelle(): string
    {
        return match ($this) {
            self::MtnMomo => 'MTN Mobile Money',
            self::OrangeMoney => 'Orange Money',
        };
    }

    /** Code USSD affiché au candidat pour initier le transfert. */
    public function ussd(): string
    {
        return match ($this) {
            self::MtnMomo => '*126#',
            self::OrangeMoney => '#150#',
        };
    }
}
