<?php

namespace App\Enums;

enum TypeAdherent: string
{
    case Etudiant = 'etudiant';
    case Enseignant = 'enseignant';
    case Chercheur = 'chercheur';
    case Personnel = 'personnel';
    case Autre = 'autre';

    public function libelle(): string
    {
        return match ($this) {
            self::Etudiant => 'Étudiant',
            self::Enseignant => 'Enseignant',
            self::Chercheur => 'Chercheur',
            self::Personnel => 'Personnel',
            self::Autre => 'Autre',
        };
    }
}
