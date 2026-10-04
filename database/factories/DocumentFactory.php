<?php

namespace Database\Factories;

use App\Enums\TypeDocument;
use App\Models\Document;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Document> */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'titre' => rtrim(fake('fr_FR')->sentence(4), '.'),
            'auteurs' => fake('fr_FR')->name(),
            'editeur' => fake('fr_FR')->company(),
            'annee_publication' => fake()->numberBetween(1990, (int) now()->year),
            'isbn' => fake()->isbn13(),
            'cote' => fake()->numerify('###.## ').strtoupper(fake()->lexify('???')),
            'type' => TypeDocument::Livre,
            'consultation_sur_place' => false,
        ];
    }

    /** Ajoute N exemplaires disponibles avec leur code-barres. */
    public function avecExemplaires(int $nombre = 1): static
    {
        return $this->afterCreating(fn (Document $document) => app(DocumentRepositoryInterface::class)
            ->ajouterExemplaires($document, $nombre));
    }
}
