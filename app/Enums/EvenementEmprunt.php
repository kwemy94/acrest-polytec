<?php

namespace App\Enums;

/** Événements d'un emprunt qui donnent lieu à un e-mail à l'étudiant. */
enum EvenementEmprunt: string
{
    case Demande = 'demande';
    case Reserve = 'reserve';
    case Refuse = 'refuse';
    case Remis = 'remis';
    case Prolonge = 'prolonge';
    case Rendu = 'rendu';
    case Rappel = 'rappel';
    case Retard = 'retard';
    case Expire = 'expire';

    public function sujet(): string
    {
        return match ($this) {
            self::Demande => 'Demande d\'emprunt enregistrée',
            self::Reserve => 'Votre document est prêt à être retiré',
            self::Refuse => 'Votre demande d\'emprunt n\'a pas été acceptée',
            self::Remis => 'Confirmation de votre emprunt',
            self::Prolonge => 'Votre emprunt est prolongé',
            self::Rendu => 'Retour de document enregistré',
            self::Rappel => 'Rappel : date de retour proche',
            self::Retard => 'Document en retard : merci de le rapporter',
            self::Expire => 'Réservation annulée : document non retiré',
        };
    }
}
