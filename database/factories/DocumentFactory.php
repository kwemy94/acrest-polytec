<?php

namespace Database\Factories;

use App\Models\Auteur;
use App\Models\Document;
use App\Models\TypeDocument;
use App\Services\Bibliotheque\CatalogueService;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Document> */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'titre' => rtrim(fake('fr_FR')->sentence(4), '.'),
            'type_document_id' => fn () => TypeDocument::firstOrCreate(['nom' => 'Livre'])->id,
            'editeur' => fake('fr_FR')->company(),
            'annee_publication' => fake()->numberBetween(1990, (int) now()->year),
            'isbn' => fake()->isbn13(),
            'langue' => 'fr',
            'consultation_sur_place' => false,
        ];
    }

    /** Auteurs du document (dans l'ordre). */
    public function auteurs(string ...$noms): static
    {
        return $this->afterCreating(fn (Document $document) => $document->auteurs()->sync(
            collect($noms)->mapWithKeys(fn ($nom, $i) => [Auteur::firstOrCreate(['nom' => $nom])->id => ['ordre' => $i + 1]])->all()
        ));
    }

    /** Ajoute N exemplaires disponibles avec leur code d'inventaire. */
    public function avecExemplaires(int $nombre = 1, array $attributs = []): static
    {
        return $this->afterCreating(fn (Document $document) => app(CatalogueService::class)->ajouterExemplaires($document, $nombre, $attributs));
    }
}
