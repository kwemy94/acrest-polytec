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
| Bibliothèque : catalogue public et espace adhérent
|--------------------------------------------------------------------------
*/
Route::prefix('bibliotheque')->name('bibliotheque.')->group(function () {
    Route::get('/', [Bibliotheque\CatalogueController::class, 'index'])->name('index');
    Route::get('/documents/{document}', [Bibliotheque\CatalogueController::class, 'show'])->name('show');

    // Ressources numériques : droits vérifiés à chaque accès (personnel ou adhérent actif)
    Route::controller(Bibliotheque\RessourceController::class)->group(function () {
        Route::get('/ressources/{ressource}', 'consulter')->name('ressources.consulter');
        Route::get('/ressources/{ressource}/fichier', 'fichier')->name('ressources.fichier');
        Route::get('/ressources/{ressource}/telecharger', 'telecharger')->middleware('throttle:30,1')->name('ressources.telecharger');
    });

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
| Administration (rôles : administrateur, bibliothécaire)
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

        Route::get('/compte', [Admin\CompteController::class, 'edit'])->name('compte');
        Route::put('/compte', [Admin\CompteController::class, 'update'])->name('compte.update');

        /* ---------- Bibliothèque : administrateurs et bibliothécaires ---------- */
        Route::middleware('role:admin,bibliothecaire')->group(function () {
            Route::get('/bibliotheque', Admin\Bibliotheque\TableauDeBordController::class)->name('bibliotheque');

            Route::resource('documents', Admin\Bibliotheque\DocumentController::class);

            Route::controller(Admin\Bibliotheque\ExemplaireController::class)->group(function () {
                Route::get('/exemplaires', 'index')->name('exemplaires.index');
                Route::post('/documents/{document}/exemplaires', 'store')->name('exemplaires.store');
                Route::get('/exemplaires/{exemplaire}', 'show')->name('exemplaires.show');
                Route::put('/exemplaires/{exemplaire}', 'update')->name('exemplaires.update');
                Route::patch('/exemplaires/{exemplaire}/localisation', 'deplacer')->name('exemplaires.deplacer');
                Route::patch('/exemplaires/{exemplaire}/statut', 'statut')->name('exemplaires.statut');
                Route::delete('/exemplaires/{exemplaire}', 'destroy')->name('exemplaires.destroy');
            });

            Route::resource('localisations', Admin\Bibliotheque\LocalisationController::class)->except(['create', 'edit']);

            Route::post('/adherents/import', [Admin\Bibliotheque\AdherentController::class, 'importer'])->name('adherents.importer');
            Route::resource('adherents', Admin\Bibliotheque\AdherentController::class)->except('destroy');

            Route::controller(Admin\Bibliotheque\EmpruntController::class)->group(function () {
                Route::get('/prets', 'index')->name('emprunts.index');
                Route::get('/prets/nouveau', 'create')->name('emprunts.create');
                Route::post('/prets', 'store')->name('emprunts.store');
                Route::get('/prets/retours', 'retours')->name('emprunts.retours');
                Route::patch('/prets/{emprunt}/retour', 'retour')->name('emprunts.retour');
                Route::patch('/prets/{emprunt}/perte', 'perte')->name('emprunts.perte');
                Route::patch('/prets/{emprunt}/prolonger', 'prolonger')->name('emprunts.prolonger');
                Route::patch('/prets/{emprunt}/{action}', 'traiter')->whereIn('action', ['valider', 'remettre', 'refuser'])->name('emprunts.traiter');
            });

            Route::controller(Admin\Bibliotheque\RessourceController::class)->group(function () {
                Route::post('/documents/{document}/ressources', 'store')->name('ressources.store');
                Route::patch('/ressources/{ressource}', 'update')->name('ressources.update');
                Route::delete('/ressources/{ressource}', 'destroy')->name('ressources.destroy');
            });

            Route::get('/journal', [Admin\Bibliotheque\JournalController::class, 'index'])->name('journal');
        });

        /* ---------- Administrateurs uniquement ---------- */
        Route::middleware('role:admin')->group(function () {
            Route::get('/inscriptions', [Admin\InscriptionController::class, 'index'])->name('inscriptions.index');
            Route::get('/inscriptions/export', [Admin\InscriptionController::class, 'export'])->name('inscriptions.export');
            Route::get('/inscriptions/{inscription}', [Admin\InscriptionController::class, 'show'])->name('inscriptions.show');
            Route::patch('/inscriptions/{inscription}/statut', [Admin\InscriptionController::class, 'statut'])->name('inscriptions.statut');
            Route::delete('/inscriptions/{inscription}', [Admin\InscriptionController::class, 'destroy'])->name('inscriptions.destroy');

            Route::get('/paiements', [Admin\PaiementController::class, 'index'])->name('paiements.index');
            Route::patch('/paiements/{paiement}', [Admin\PaiementController::class, 'traiter'])->name('paiements.traiter');

            Route::get('/newsletter', [Admin\NewsletterController::class, 'index'])->name('newsletter.index');
            Route::get('/newsletter/export', [Admin\NewsletterController::class, 'export'])->name('newsletter.export');

            Route::resource('utilisateurs', Admin\UtilisateurController::class)->except(['show', 'destroy'])
                ->parameters(['utilisateurs' => 'utilisateur']);

            Route::controller(Admin\Bibliotheque\ReferentielController::class)->group(function () {
                Route::get('/referentiels', 'index')->name('referentiels');
                Route::post('/referentiels/types', 'enregistrerType')->name('types-documents.store');
                Route::put('/referentiels/types/{type}', 'enregistrerType')->name('types-documents.update');
                Route::delete('/referentiels/types/{type}', 'supprimerType')->name('types-documents.destroy');
                Route::post('/referentiels/categories', 'enregistrerCategorie')->name('categories.store');
                Route::put('/referentiels/categories/{categorie}', 'enregistrerCategorie')->name('categories.update');
                Route::delete('/referentiels/categories/{categorie}', 'supprimerCategorie')->name('categories.destroy');
                Route::put('/referentiels/parametres', 'enregistrerParametres')->name('parametres.update');
            });
        });
    });
});
