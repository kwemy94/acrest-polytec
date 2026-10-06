<?php

namespace App\Enums;

/**
 * Cycle d'un emprunt.
 * En ligne : Demande → Reserve (exemplaire mis de côté) → EnCours → Rendu.
 * Au guichet : EnCours → Rendu (ou Perdu).
 * Une demande peut être refusée, ou annulée (par l'adhérent ou faute de retrait à temps).
 */
enum StatutEmprunt: string
{
    case Demande = 'demande';
    case Reserve = 'reserve';
    case EnCours = 'en_cours';
    case Rendu = 'rendu';
    case Perdu = 'perdu';
    case Refuse = 'refuse';
    case Annule = 'annule';

    public function libelle(): string
    {
        return match ($this) {
            self::Demande => 'Demande en attente',
            self::Reserve => 'Prêt à retirer',
            self::EnCours => 'En cours',
            self::Rendu => 'Rendu',
            self::Perdu => 'Déclaré perdu',
            self::Refuse => 'Refusé',
            self::Annule => 'Annulé',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::Demande => 'warning',
            self::Reserve => 'info',
            self::EnCours => 'success',
            self::Rendu, self::Annule => 'neutre',
            self::Refuse, self::Perdu => 'danger',
        };
    }

    /** Statuts qui comptent dans le quota de l'adhérent. */
    public static function actifs(): array
    {
        return [self::Demande, self::Reserve, self::EnCours];
    }

    public function estActif(): bool
    {
        return in_array($this, self::actifs(), true);
    }
}
