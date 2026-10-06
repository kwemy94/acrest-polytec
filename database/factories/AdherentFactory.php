<?php

namespace Database\Factories;

use App\Enums\StatutAdherent;
use App\Enums\TypeAdherent;
use App\Models\Adherent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Adherent> */
class AdherentFactory extends Factory
{
    protected $model = Adherent::class;

    public function definition(): array
    {
        return [
            'matricule' => 'ADH-'.strtoupper(fake()->unique()->bothify('####??')),
            'nom' => strtoupper(fake('fr_FR')->lastName()),
            'prenom' => fake('fr_FR')->firstName(),
            'telephone' => '6'.fake()->numerify('########'),
            'email' => fake()->unique()->safeEmail(),
            'type' => TypeAdherent::Etudiant,
            'date_inscription' => today()->subMonth(),
            'date_expiration' => today()->addYear(),
            'statut' => StatutAdherent::Actif,
        ];
    }

    public function type(TypeAdherent $type): static
    {
        return $this->state(['type' => $type]);
    }

    public function suspendu(): static
    {
        return $this->state(['statut' => StatutAdherent::Suspendu]);
    }

    public function expire(): static
    {
        return $this->state(['date_expiration' => today()->subDay()]);
    }
}
