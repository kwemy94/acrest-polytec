<?php

namespace App\Services\Inscription;

enum Etape: string
{
    case Identite = 'identite';
    case Coordonnees = 'coordonnees';
    case Diplome = 'diplome';
    case Formation = 'formation';
    case Verification = 'verification';

    public function numero(): int
    {
        return array_search($this, self::cases(), true) + 1;
    }

    public function titre(): string
    {
        return match ($this) {
            self::Identite => 'Identité',
            self::Coordonnees => 'Coordonnées',
            self::Diplome => 'Diplôme',
            self::Formation => 'Formation',
            self::Verification => 'Vérification',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Identite => 'Renseignez votre état civil tel qu\'il figure sur votre pièce d\'identité.',
            self::Coordonnees => 'Comment vous joindre, vous et vos parents ou tuteurs.',
            self::Diplome => 'Le diplôme qui vous donne accès à la formation.',
            self::Formation => 'Choisissez jusqu\'à trois spécialités, par ordre de préférence.',
            self::Verification => 'Relisez vos informations avant d\'envoyer votre dossier.',
        };
    }

    public function icone(): string
    {
        return match ($this) {
            self::Identite => 'bi-person-vcard',
            self::Coordonnees => 'bi-telephone',
            self::Diplome => 'bi-mortarboard',
            self::Formation => 'bi-diagram-3',
            self::Verification => 'bi-clipboard-check',
        };
    }

    public function precedente(): ?self
    {
        return self::cases()[$this->numero() - 2] ?? null;
    }

    public function suivante(): ?self
    {
        return self::cases()[$this->numero()] ?? null;
    }
}
