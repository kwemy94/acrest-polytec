<?php

namespace Database\Factories;

use App\Enums\Sexe;
use App\Enums\StatutInscription;
use App\Models\Inscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Inscription> */
class InscriptionFactory extends Factory
{
    protected $model = Inscription::class;

    public function definition(): array
    {
        $sexe = fake()->randomElement(Sexe::cases());

        return [
            'code' => 'ISAP-'.now()->format('y').'-'.strtoupper(fake()->unique()->bothify('??####')),
            'nom' => strtoupper(fake('fr_FR')->lastName()),
            'prenom' => fake('fr_FR')->firstName($sexe === Sexe::Masculin ? 'male' : 'female'),
            'sexe' => $sexe,
            'date_naissance' => fake()->dateTimeBetween('-30 years', '-17 years'),
            'lieu_naissance' => fake()->randomElement(['Dschang', 'Bafoussam', 'Mbouda', 'Douala', 'Yaoundé', 'Bertoua', 'Bamenda']),
            'pays' => 'Cameroun',
            'cni' => fake()->unique()->numerify('1#########'),
            'telephone' => '6'.fake()->numerify('########'),
            'email' => fake()->unique()->safeEmail(),
            'nom_pere' => fake('fr_FR')->name('male'),
            'nom_mere' => fake('fr_FR')->name('female'),
            'contact_parent' => '6'.fake()->numerify('########'),
            'diplome' => fake()->randomElement(['BACC', 'GCE A/L', 'PROBATOIRE', 'BTS']),
            'option_diplome' => fake()->randomElement(['C', 'D', 'A4', 'TI', 'F3', null]),
            'annee_obtention' => fake()->numberBetween(2018, (int) now()->year),
            'statut' => StatutInscription::EnAttente,
        ];
    }
}
