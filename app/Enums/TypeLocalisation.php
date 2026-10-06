<?php

namespace App\Enums;

enum TypeLocalisation: string
{
    case Bibliotheque = 'bibliotheque';
    case Salle = 'salle';
    case Rayon = 'rayon';
    case Etagere = 'etagere';
    case Niveau = 'niveau';
    case Magasin = 'magasin';
    case Autre = 'autre';

    public function libelle(): string
    {
        return match ($this) {
            self::Bibliotheque => 'Bibliothèque',
            self::Salle => 'Salle',
            self::Rayon => 'Rayon',
            self::Etagere => 'Étagère',
            self::Niveau => 'Niveau',
            self::Magasin => 'Magasin / réserve',
            self::Autre => 'Autre',
        };
    }

    public function icone(): string
    {
        return match ($this) {
            self::Bibliotheque => 'bi-building',
            self::Salle => 'bi-door-open',
            self::Rayon => 'bi-signpost-split',
            self::Etagere => 'bi-bookshelf',
            self::Niveau => 'bi-layers',
            self::Magasin => 'bi-box-seam',
            self::Autre => 'bi-geo-alt',
        };
    }
}
