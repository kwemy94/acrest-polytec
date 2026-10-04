<?php

namespace App\Enums;

/**
 * Cycle d'un emprunt : Demande → Reserve (exemplaire mis de côté) → EnCours → Rendu.
 * Une demande peut être refusée, ou annulée (par l'étudiant ou faute de retrait à temps).
 */
enum StatutEmprunt: string
{
    case Demande = 'demande';
    case Reserve = 'reserve';
    case EnCours = 'en_cours';
    case Rendu = 'rendu';
    case Refuse = 'refuse';
    case Annule = 'annule';

    public function libelle(): string
    {
        return match ($this) {
            self::Demande => 'Demande en attente',
            self::Reserve => 'Prêt à retirer',
            self::EnCours => 'En cours',
            self::Rendu => 'Rendu',
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
            self::Rendu => 'neutre',
            self::Refuse => 'danger',
            self::Annule => 'neutre',
        };
    }

    /** Statuts qui comptent dans le quota d'emprunts de l'étudiant. */
    public static function actifs(): array
    {
        return [self::Demande, self::Reserve, self::EnCours];
    }

    public function estActif(): bool
    {
        return in_array($this, self::actifs(), true);
    }
}
