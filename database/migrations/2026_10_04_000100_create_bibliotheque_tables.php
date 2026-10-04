<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Notice bibliographique
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('auteurs');
            $table->string('editeur')->nullable();
            $table->unsignedSmallInteger('annee_publication')->nullable();
            $table->string('isbn', 20)->nullable()->index();
            $table->string('cote', 30)->nullable();
            $table->string('type', 20)->index();
            $table->foreignId('filiere_id')->nullable()->constrained()->nullOnDelete();
            $table->text('resume')->nullable();
            $table->boolean('consultation_sur_place')->default(false);
            $table->timestamps();
        });

        // Exemplaires physiques d'une notice
        Schema::create('exemplaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20)->nullable()->unique();
            $table->string('etat', 20)->default('disponible')->index();
            $table->timestamps();
        });

        Schema::create('emprunts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_id')->constrained()->restrictOnDelete();
            $table->foreignId('exemplaire_id')->nullable()->constrained()->nullOnDelete();
            $table->string('statut', 20)->default('demande')->index();
            $table->string('message', 500)->nullable();
            $table->string('motif', 500)->nullable();

            $table->date('retirer_avant')->nullable();
            $table->timestamp('date_pret')->nullable();
            $table->date('date_retour_prevue')->nullable()->index();
            $table->timestamp('date_retour')->nullable();
            $table->unsignedTinyInteger('prolongations')->default(0);

            $table->timestamp('rappel_envoye_le')->nullable();
            $table->timestamp('derniere_relance_le')->nullable();

            $table->foreignId('traite_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['document_id', 'statut']);
            $table->index(['inscription_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emprunts');
        Schema::dropIfExists('exemplaires');
        Schema::dropIfExists('documents');
    }
};
