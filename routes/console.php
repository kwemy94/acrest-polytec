<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

// Crée (ou réinitialise) un compte administrateur : php artisan acrest:admin
Artisan::command('acrest:admin', function () {
    $name = text('Nom', default: 'Administrateur', required: true);
    $email = text('Adresse e-mail', required: true, validate: fn ($v) => Validator::make(['e' => $v], ['e' => 'email'])->fails() ? 'Adresse invalide.' : null);
    $pwd = password('Mot de passe (8 caractères minimum)', required: true, validate: fn ($v) => strlen($v) < 8 ? '8 caractères minimum.' : null);

    User::updateOrCreate(['email' => $email], ['name' => $name, 'password' => $pwd]);

    $this->info("Compte administrateur prêt : {$email}");
})->purpose('Créer ou réinitialiser un compte administrateur ACREST');
