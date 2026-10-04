<?php

namespace App\Enums;

enum TypeDocument: string
{
    case Livre = 'livre';
    case Memoire = 'memoire';
    case Revue = 'revue';
    case Rapport = 'rapport';
    case Cours = 'cours';
    case Multimedia = 'multimedia';

    public function libelle(): string
    {
        return match ($this) {
            self::Livre => 'Livre',
            self::Memoire => 'Mémoire / rapport de stage',
            self::Revue => 'Revue / périodique',
            self::Rapport => 'Rapport technique',
            self::Cours => 'Support de cours',
            self::Multimedia => 'CD / DVD / multimédia',
        };
    }

    public function icone(): string
    {
        return match ($this) {
            self::Livre => 'bi-book',
            self::Memoire => 'bi-mortarboard',
            self::Revue => 'bi-newspaper',
            self::Rapport => 'bi-file-earmark-text',
            self::Cours => 'bi-journal-text',
            self::Multimedia => 'bi-disc',
        };
    }
}
