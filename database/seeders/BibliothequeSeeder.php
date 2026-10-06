<?php

namespace Database\Seeders;

use App\Enums\EtatPhysique;
use App\Enums\TypeAdherent;
use App\Enums\TypeLocalisation;
use App\Models\Adherent;
use App\Models\Categorie;
use App\Models\Document;
use App\Models\Localisation;
use App\Models\TypeDocument;
use App\Services\Bibliotheque\CatalogueService;
use Illuminate\Database\Seeder;

/** Fonds documentaire de démonstration : php artisan db:seed --class=BibliothequeSeeder */
class BibliothequeSeeder extends Seeder
{
    public function run(CatalogueService $catalogue): void
    {
        /* ---------- Plan de la bibliothèque ---------- */
        $espace = function (string $nom, TypeLocalisation $type, ?Localisation $parent = null): Localisation {
            return Localisation::firstOrCreate(['nom' => $nom, 'parent_id' => $parent?->id], ['type' => $type]);
        };
        $principale = $espace('Bibliothèque principale', TypeLocalisation::Bibliotheque);
        $sciences = $espace('Salle Sciences', TypeLocalisation::Salle, $principale);
        $energie = $espace('Rayon Énergies renouvelables', TypeLocalisation::Rayon, $sciences);
        $electro = $espace('Rayon Électrotechnique', TypeLocalisation::Rayon, $sciences);
        $etageres = [
            'solaire' => $espace('Niveau 2', TypeLocalisation::Niveau, $espace('Étagère A', TypeLocalisation::Etagere, $energie)),
            'eolien' => $espace('Niveau 3', TypeLocalisation::Niveau, $espace('Étagère B', TypeLocalisation::Etagere, $energie)),
            'electro' => $espace('Étagère C', TypeLocalisation::Etagere, $electro),
            'memoires' => $espace('Mémoires et rapports', TypeLocalisation::Rayon, $espace('Salle de lecture', TypeLocalisation::Salle, $principale)),
            'magasin' => $espace('Magasin', TypeLocalisation::Magasin, $principale),
        ];

        /* ---------- Catégories ---------- */
        $categorie = fn (string $nom) => Categorie::firstOrCreate(['nom' => $nom])->id;
        $type = fn (string $nom) => TypeDocument::firstOrCreate(['nom' => $nom], ['ordre' => 99])->id;

        /* ---------- Fonds : [titre, sous-titre, auteurs, éditeur, année, type, catégorie, nb, rangement, mots-clés, langue] ---------- */
        $fonds = [
            ['Énergie solaire photovoltaïque', null, ['Anne Labouret', 'Michel Villoz'], 'Dunod', 2018, 'Livre', 'Énergie solaire', 3, 'solaire', 'photovoltaïque, solaire, dimensionnement', 'fr'],
            ['Installations photovoltaïques', 'Conception et dimensionnement', ['Alain Ricaud'], 'Le Moniteur', 2020, 'Manuel', 'Énergie solaire', 2, 'solaire', 'photovoltaïque, installation', 'fr'],
            ['Électrotechnique', 'Machines électriques', ['Théodore Wildi'], 'De Boeck', 2015, 'Livre', 'Électrotechnique', 2, 'electro', 'moteurs, transformateurs', 'fr'],
            ['Les éoliennes', 'Principes et applications', ['Bernard Multon'], 'Ellipses', 2017, 'Livre', 'Énergie éolienne', 1, 'eolien', 'éolien, aérogénérateur', 'fr'],
            ['Biogaz et méthanisation en milieu rural', null, ['Collectif ACREST'], 'ACREST', 2021, 'Rapport', 'Biomasse', 1, 'memoires', 'biogaz, méthanisation', 'fr'],
            ['Dimensionnement d\'un mini-réseau solaire à Bangang', null, ['Paul Tchoffo'], null, 2024, 'Mémoire', 'Énergie solaire', 1, 'memoires', 'mini-réseau, électrification rurale', 'fr'],
            ['Renewable Energy', 'Power for a Sustainable Future', ['Godfrey Boyle'], 'Oxford University Press', 2012, 'Livre', 'Énergies renouvelables', 2, 'eolien', 'renewable, energy', 'en'],
            ['Revue des énergies renouvelables', 'Numéro 42', ['CDER'], 'CDER', 2023, 'Revue', 'Énergies renouvelables', 1, 'magasin', 'revue', 'fr'],
        ];

        foreach ($fonds as [$titre, $sousTitre, $auteurs, $editeur, $annee, $nomType, $nomCategorie, $nombre, $rangement, $motsCles, $langue]) {
            if (Document::where('titre', $titre)->exists()) {
                continue;
            }

            $document = $catalogue->creerDocument([
                'titre' => $titre,
                'sous_titre' => $sousTitre,
                'type_document_id' => $type($nomType),
                'categorie_id' => $categorie($nomCategorie),
                'editeur' => $editeur,
                'annee_publication' => $annee,
                'langue' => $langue,
                'mots_cles' => $motsCles,
            ], $auteurs);

            $catalogue->ajouterExemplaires($document, $nombre, [
                'localisation_id' => $etageres[$rangement]->id,
                'date_acquisition' => today()->subMonths(rand(1, 36))->toDateString(),
                'source_acquisition' => fake()->randomElement(['Achat', 'Don']),
                'etat_physique' => fake()->randomElement([EtatPhysique::Neuf, EtatPhysique::Bon, EtatPhysique::Moyen])->value,
            ]);
        }

        /* ---------- Adhérents ---------- */
        foreach ([TypeAdherent::Etudiant, TypeAdherent::Etudiant, TypeAdherent::Enseignant, TypeAdherent::Personnel] as $typeAdherent) {
            Adherent::factory()->type($typeAdherent)->create();
        }
    }
}
