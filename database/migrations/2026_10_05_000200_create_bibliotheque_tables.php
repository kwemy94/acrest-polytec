<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Bibliothèque : un document décrit l'œuvre, un exemplaire en est une copie physique,
 * une localisation indique où il se trouve, un emprunt décrit son utilisation temporaire,
 * une ressource numérique représente sa version électronique.
 */
return new class extends Migration
{
    public function up(): void
    {
        /* ---------- Référentiels ---------- */

        Schema::create('types_documents', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 60)->unique();
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        // Liste initiale, modifiable ensuite par l'administrateur.
        DB::table('types_documents')->insert(collect(['Livre', 'Mémoire', 'Thèse', 'Article', 'Rapport', 'Revue', 'Manuel', 'Autre'])
            ->map(fn ($nom, $i) => ['nom' => $nom, 'ordre' => ($i + 1) * 10, 'actif' => true, 'created_at' => now(), 'updated_at' => now()])
            ->all());

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100)->unique();
            $table->string('description', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('auteurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->timestamps();
        });

        Schema::create('parametres', function (Blueprint $table) {
            $table->string('cle', 80)->primary();
            $table->text('valeur')->nullable();
            $table->timestamps();
        });

        /* ---------- Catalogue ---------- */

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('sous_titre')->nullable();
            $table->foreignId('type_document_id')->constrained('types_documents')->restrictOnDelete();
            $table->foreignId('categorie_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('editeur')->nullable();
            $table->unsignedSmallInteger('annee_publication')->nullable();
            $table->string('isbn', 20)->nullable()->index(); // ISBN ou ISSN
            $table->string('langue', 10)->default('fr')->index();
            $table->text('description')->nullable();
            $table->string('mots_cles', 500)->nullable();
            $table->unsignedSmallInteger('nombre_pages')->nullable();
            $table->string('cote', 30)->nullable();
            $table->boolean('consultation_sur_place')->default(false);
            $table->foreignId('cree_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('titre');
        });

        Schema::create('document_auteur', function (Blueprint $table) {
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('auteur_id')->constrained('auteurs')->cascadeOnDelete();
            $table->unsignedTinyInteger('ordre')->default(1);
            $table->primary(['document_id', 'auteur_id']);
        });

        /* ---------- Espaces physiques et exemplaires ---------- */

        Schema::create('localisations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('localisations')->restrictOnDelete();
            $table->string('nom', 100);
            $table->string('type', 20);
            $table->string('code', 30)->nullable();
            $table->string('description', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('exemplaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->string('code_inventaire', 30)->unique();
            $table->string('code_barres', 50)->nullable()->unique();
            $table->date('date_acquisition')->nullable();
            $table->string('source_acquisition', 100)->nullable(); // achat, don, dépôt…
            $table->string('etat_physique', 20)->default('bon');
            $table->string('statut', 20)->default('disponible')->index();
            $table->foreignId('localisation_id')->nullable()->constrained('localisations')->restrictOnDelete();
            $table->string('notes', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('historique_localisations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exemplaire_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ancienne_localisation_id')->nullable()->constrained('localisations')->nullOnDelete();
            $table->foreignId('nouvelle_localisation_id')->nullable()->constrained('localisations')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('motif', 255)->nullable();
            $table->timestamps();
        });

        /* ---------- Adhérents et prêts ---------- */

        Schema::create('adherents', function (Blueprint $table) {
            $table->id();
            $table->string('matricule', 30)->unique();
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('email')->nullable()->index();
            $table->string('type', 20)->index();
            $table->date('date_inscription');
            $table->date('date_expiration')->nullable();
            $table->string('statut', 20)->default('actif')->index();
            $table->foreignId('inscription_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('notes', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('emprunts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adherent_id')->constrained()->restrictOnDelete();
            $table->foreignId('document_id')->constrained()->restrictOnDelete();
            $table->foreignId('exemplaire_id')->nullable()->constrained()->nullOnDelete();
            // Renseigné uniquement pendant le prêt : l'index unique garantit un seul prêt actif par exemplaire.
            $table->foreignId('exemplaire_actif_id')->nullable()->unique()->constrained('exemplaires')->nullOnDelete();
            $table->string('statut', 20)->default('demande')->index();
            $table->string('canal', 20)->default('guichet');
            $table->string('message', 500)->nullable();
            $table->string('motif', 500)->nullable();

            $table->date('retirer_avant')->nullable();
            $table->timestamp('date_pret')->nullable();
            $table->date('date_retour_prevue')->nullable()->index();
            $table->timestamp('date_retour')->nullable();
            $table->unsignedSmallInteger('jours_retard')->nullable();
            $table->string('etat_retour', 20)->nullable();
            $table->unsignedTinyInteger('prolongations')->default(0);

            $table->timestamp('rappel_envoye_le')->nullable();
            $table->timestamp('derniere_relance_le')->nullable();

            $table->foreignId('traite_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['document_id', 'statut']);
            $table->index(['adherent_id', 'statut']);
        });

        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exemplaire_id')->constrained()->cascadeOnDelete();
            $table->foreignId('emprunt_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('adherent_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 20)->index();
            $table->string('description', 500)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        /* ---------- Ressources numériques ---------- */

        Schema::create('ressources_numeriques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->string('titre')->nullable();
            $table->string('fichier');
            $table->string('nom_original');
            $table->string('mime', 120);
            $table->string('type', 20);
            $table->unsignedBigInteger('taille');
            $table->string('version', 20)->default('1');
            $table->string('niveau_acces', 30)->index();
            $table->foreignId('ajoute_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        /* ---------- Traçabilité ---------- */

        Schema::create('journal_activites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('adherent_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50)->index();
            $table->nullableMorphs('sujet');
            $table->string('description', 500);
            $table->json('proprietes')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        foreach (['journal_activites', 'ressources_numeriques', 'incidents', 'emprunts', 'adherents', 'historique_localisations',
            'exemplaires', 'localisations', 'document_auteur', 'documents', 'parametres', 'auteurs', 'categories', 'types_documents'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
