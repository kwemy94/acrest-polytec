<?php

namespace App\Enums;

enum StatutExemplaire: string
{
    case Disponible = 'disponible';
    case Emprunte = 'emprunte';
    case Reserve = 'reserve';
    case Perdu = 'perdu';
    case EnReparation = 'en_reparation';
    case Retire = 'retire';

    public function libelle(): string
    {
        return match ($this) {
            self::Disponible => 'Disponible',
            self::Emprunte => 'Emprunté',
            self::Reserve => 'Réservé',
            self::Perdu => 'Perdu',
            self::EnReparation => 'En réparation',
            self::Retire => 'Retiré du catalogue',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::Disponible => 'success',
            self::Emprunte => 'warning',
            self::Reserve => 'info',
            self::Perdu => 'danger',
            self::EnReparation, self::Retire => 'neutre',
        };
    }

    /** Statuts gérés par la circulation (prêt, réservation) : non modifiables à la main. */
    public function enCirculation(): bool
    {
        return in_array($this, [self::Emprunte, self::Reserve], true);
    }

    /** Statuts qu'un bibliothécaire peut attribuer manuellement. */
    public static function manuels(): array
    {
        return [self::Disponible, self::Perdu, self::EnReparation, self::Retire];
    }

    /** Exemplaires qui font partie du fonds (hors perdus et retirés). */
    public static function fondsActif(): array
    {
        return [self::Disponible, self::Emprunte, self::Reserve, self::EnReparation];
    }
}
