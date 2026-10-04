<?php

namespace Database\Seeders;

use App\Enums\TypeDocument;
use App\Models\Document;
use App\Models\Filiere;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use Illuminate\Database\Seeder;

/** Fonds documentaire de démonstration : php artisan db:seed --class=BibliothequeSeeder */
class BibliothequeSeeder extends Seeder
{
    public function run(DocumentRepositoryInterface $documents): void
    {
        $filieres = Filiere::pluck('id');

        $fonds = [
            ['Énergie solaire photovoltaïque', 'Anne Labouret, Michel Villoz', 'Dunod', 2018, TypeDocument::Livre, 3, 'fr'],
            ['Installations photovoltaïques : conception et dimensionnement', 'Alain Ricaud', 'Le Moniteur', 2020, TypeDocument::Livre, 2, 'fr'],
            ['Électrotechnique : machines électriques', 'Théodore Wildi', 'De Boeck', 2015, TypeDocument::Livre, 2, 'fr'],
            ['Les éoliennes : principes et applications', 'Bernard Multon', 'Ellipses', 2017, TypeDocument::Livre, 1, 'fr'],
            ['Biogaz et méthanisation en milieu rural', 'Collectif ACREST', 'ACREST', 2021, TypeDocument::Rapport, 1, 'fr'],
            ['Hydraulique et pico-centrales', 'Jean Kamdem', 'Presses universitaires de Dschang', 2019, TypeDocument::Livre, 2, 'fr'],
            ['Dimensionnement d\'un mini-réseau solaire à Bangang', 'Paul Tchoffo', null, 2024, TypeDocument::Memoire, 1, 'fr'],
            ['Cours d\'électricité générale — BTS 1re année', 'Équipe pédagogique ACREST', 'ACREST', 2025, TypeDocument::Cours, 5, 'fr'],
            ['Revue des énergies renouvelables — n° 42', 'CDER', 'CDER', 2023, TypeDocument::Revue, 1, 'fr'],
            ['Renewable Energy: Power for a Sustainable Future', 'Godfrey Boyle', 'Oxford University Press', 2012, TypeDocument::Livre, 2, 'en'],
            ['Dictionnaire technique de l\'électricité', 'Collectif', 'Larousse', 2012, TypeDocument::Livre, 1, 'fr', true],
        ];

        foreach ($fonds as $ligne) {
            [$titre, $auteurs, $editeur, $annee, $type, $nombre, $langue] = $ligne;

            if (Document::where('titre', $titre)->exists()) {
                continue;
            }

            $documents->creerAvecExemplaires([
                'titre' => $titre,
                'auteurs' => $auteurs,
                'editeur' => $editeur,
                'annee_publication' => $annee,
                'type' => $type,
                'langue' => $langue,
                'filiere_id' => $filieres->isNotEmpty() ? $filieres->random() : null,
                'consultation_sur_place' => $ligne[7] ?? false,
            ], $nombre);
        }
    }
}
