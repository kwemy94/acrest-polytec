<?php

namespace Database\Seeders;

use App\Models\Filiere;
use App\Models\Specialite;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

/** Importe les filières et spécialités du site ACREST d'origine (idempotent). */
class FormationSeeder extends Seeder
{
    public function run(): void
    {
        $data = require database_path('seeders/data/formations.php');

        $ids = [];
        foreach ($data['filieres'] as $filiere) {
            $ids[$filiere['slug']] = Filiere::updateOrCreate(['slug' => $filiere['slug']], $filiere)->id;
        }

        foreach ($data['specialites'] as $specialite) {
            $slugFiliere = $specialite['filiere'];
            unset($specialite['filiere']);

            Specialite::updateOrCreate(
                ['slug' => $specialite['slug']],
                $specialite + ['filiere_id' => $ids[$slugFiliere]],
            );
        }

        Cache::forget('menu.filieres');
    }
}
