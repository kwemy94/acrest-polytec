<?php

namespace Database\Seeders;

use App\Enums\OperateurPaiement;
use App\Enums\StatutInscription;
use App\Enums\StatutPaiement;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\Specialite;
use Illuminate\Database\Seeder;

/** Données fictives pour tester l'administration : php artisan db:seed --class=DemoSeeder */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $specialites = Specialite::pluck('id');

        Inscription::factory()->count(40)->create()->each(function (Inscription $inscription) use ($specialites) {
            $choix = $specialites->random(rand(1, 3))->values();
            $inscription->specialites()->attach($choix->mapWithKeys(fn ($id, $i) => [$id => ['rang' => $i + 1]])->all());

            if (rand(0, 3) > 0) {
                $statut = fake()->randomElement(StatutPaiement::cases());
                Paiement::create([
                    'inscription_id' => $inscription->id,
                    'operateur' => fake()->randomElement(OperateurPaiement::cases()),
                    'montant' => config('acrest.paiement.frais_inscription'),
                    'telephone' => '2376'.fake()->numerify('########'),
                    'reference' => strtoupper(fake()->unique()->bothify('MP######.####.?#####')),
                    'statut' => $statut,
                    'traite_le' => $statut === StatutPaiement::EnAttente ? null : now(),
                ]);
                if ($statut === StatutPaiement::Valide && rand(0, 1)) {
                    $inscription->update(['statut' => StatutInscription::Validee]);
                }
            }
        });
    }
}
