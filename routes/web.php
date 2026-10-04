<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Bibliotheque;
use App\Http\Controllers\DossierController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\SpecialiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Site public
|--------------------------------------------------------------------------
*/
Route::get('/', HomeController::class)->name('accueil');
Route::get('/acrest', [PageController::class, 'acrest'])->name('acrest');
Route::get('/technologies-appropriees', [PageController::class, 'technologies'])->name('technologies');
Route::get('/logement', [PageController::class, 'logement'])->name('logement');

Route::get('/filieres', [FiliereController::class, 'index'])->name('filieres.index');
Route::get('/filieres/{slug}', [FiliereController::class, 'show'])->name('filieres.show');
Route::get('/specialites', [SpecialiteController::class, 'index'])->name('specialites.index');
Route::get('/specialites/{slug}', [SpecialiteController::class, 'show'])->name('specialites.show');

Route::post('/newsletter', NewsletterController::class)->middleware('throttle:6,1')->name('newsletter');

// Inscription en plusieurs étapes
Route::prefix('inscription')->name('inscription.')->controller(InscriptionController::class)->group(function () {
    Route::get('/', 'debut')->name('debut');
    Route::get('/recommencer', 'recommencer')->name('recommencer');
    Route::get('/{etape}', 'afficher')->name('etape');
    Route::post('/identite', 'identite')->name('identite');
    Route::post('/coordonnees', 'coordonnees')->name('coordonnees');
    Route::post('/diplome', 'diplome')->name('diplome');
    Route::post('/formation', 'formation')->name('formation');
    Route::post('/verification', 'finaliser')->middleware('throttle:5,1')->name('finaliser');
});

// Suivi de dossier
Route::controller(DossierController::class)->group(function () {
    Route::get('/suivi', 'recherche')->name('dossier.recherche');
    Route::post('/suivi', 'rechercher')->middleware('throttle:recherche-dossier')->name('dossier.rechercher');
    Route::get('/suivi/code-oublie', 'retrouver')->name('dossier.retrouver');
    Route::post('/suivi/code-oublie', 'envoyerCode')->middleware('throttle:recherche-dossier')->name('dossier.envoyer-code');
    Route::get('/dossier/{code}', 'show')->name('dossier.show');
});

// Paiement des frais
Route::get('/paiement', [PaiementController::class, 'create'])->name('paiement.create');
Route::post('/paiement', [PaiementController::class, 'store'])->middleware('throttle:recherche-dossier')->name('paiement.store');

/*
|--------------------------------------------------------------------------
| Bibliothèque : catalogue public, espace emprunts des étudiants
|--------------------------------------------------------------------------
*/
Route::prefix('bibliotheque')->name('bibliotheque.')->group(function () {
    Route::get('/', [Bibliotheque\CatalogueController::class, 'index'])->name('index');
    Route::get('/documents/{document}', [Bibliotheque\CatalogueController::class, 'show'])->name('show');

    // Version numérique (étudiants connectés et administrateurs)
    Route::get('/documents/{document}/lire', [Bibliotheque\LectureController::class, 'lire'])->name('lire');
    Route::get('/documents/{document}/pdf', [Bibliotheque\LectureController::class, 'fichier'])->name('pdf');
    Route::get('/documents/{document}/telecharger', [Bibliotheque\LectureController::class, 'telecharger'])->middleware('throttle:20,1')->name('telecharger');

    Route::get('/connexion', [Bibliotheque\LecteurController::class, 'create'])->name('connexion');
    Route::post('/connexion', [Bibliotheque\LecteurController::class, 'store'])->middleware('throttle:recherche-dossier')->name('connexion.store');

    Route::middleware('lecteur')->group(function () {
        Route::post('/deconnexion', [Bibliotheque\LecteurController::class, 'destroy'])->name('deconnexion');
        Route::get('/mes-emprunts', [Bibliotheque\EmpruntController::class, 'index'])->name('emprunts');
        Route::post('/documents/{document}/demande', [Bibliotheque\EmpruntController::class, 'store'])->middleware('throttle:10,1')->name('demander');
        Route::post('/emprunts/{emprunt}/annuler', [Bibliotheque\EmpruntController::class, 'annuler'])->name('annuler');
        Route::post('/emprunts/{emprunt}/prolonger', [Bibliotheque\EmpruntController::class, 'prolonger'])->name('prolonger');
    });
});

/*
|--------------------------------------------------------------------------
| Administration
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/connexion', [Admin\AuthController::class, 'create'])->name('login');
        Route::post('/connexion', [Admin\AuthController::class, 'store'])->name('login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/deconnexion', [Admin\AuthController::class, 'destroy'])->name('logout');
        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        Route::get('/inscriptions', [Admin\InscriptionController::class, 'index'])->name('inscriptions.index');
        Route::get('/inscriptions/export', [Admin\InscriptionController::class, 'export'])->name('inscriptions.export');
        Route::get('/inscriptions/{inscription}', [Admin\InscriptionController::class, 'show'])->name('inscriptions.show');
        Route::patch('/inscriptions/{inscription}/statut', [Admin\InscriptionController::class, 'statut'])->name('inscriptions.statut');
        Route::delete('/inscriptions/{inscription}', [Admin\InscriptionController::class, 'destroy'])->name('inscriptions.destroy');

        Route::get('/paiements', [Admin\PaiementController::class, 'index'])->name('paiements.index');
        Route::patch('/paiements/{paiement}', [Admin\PaiementController::class, 'traiter'])->name('paiements.traiter');

        // Bibliothèque
        Route::resource('documents', Admin\DocumentController::class);
        Route::post('/documents/{document}/exemplaires', [Admin\DocumentController::class, 'ajouterExemplaires'])->name('documents.exemplaires.store');
        Route::patch('/exemplaires/{exemplaire}', [Admin\DocumentController::class, 'etatExemplaire'])->name('exemplaires.update');
        Route::delete('/exemplaires/{exemplaire}', [Admin\DocumentController::class, 'supprimerExemplaire'])->name('exemplaires.destroy');

        Route::get('/emprunts', [Admin\EmpruntController::class, 'index'])->name('emprunts.index');
        Route::get('/emprunts/nouveau', [Admin\EmpruntController::class, 'create'])->name('emprunts.create');
        Route::post('/emprunts', [Admin\EmpruntController::class, 'store'])->name('emprunts.store');
        Route::patch('/emprunts/{emprunt}/{action}', [Admin\EmpruntController::class, 'traiter'])
            ->whereIn('action', ['valider', 'refuser', 'remettre', 'retour', 'prolonger'])
            ->name('emprunts.traiter');

        Route::get('/newsletter', [Admin\NewsletterController::class, 'index'])->name('newsletter.index');
        Route::get('/newsletter/export', [Admin\NewsletterController::class, 'export'])->name('newsletter.export');

        Route::get('/compte', [Admin\CompteController::class, 'edit'])->name('compte');
        Route::put('/compte', [Admin\CompteController::class, 'update'])->name('compte.update');
    });
});
