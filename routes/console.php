<?php

use App\Models\User;
use App\Services\Bibliotheque\EmpruntService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
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

// Réservations expirées, rappels d'échéance et relances de retard : php artisan bibliotheque:echeances
Artisan::command('bibliotheque:echeances', function (EmpruntService $service) {
    $bilan = $service->traiterEcheances();

    $this->info("Réservations expirées : {$bilan['expirees']} · rappels envoyés : {$bilan['rappels']} · relances de retard : {$bilan['relances']}");
})->purpose('Traiter les échéances de prêt de la bibliothèque et envoyer les e-mails');

// Nécessite la tâche cron « php artisan schedule:run » chaque minute sur le serveur.
Schedule::command('bibliotheque:echeances')->dailyAt('07:00');
