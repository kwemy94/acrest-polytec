<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();

            // Identité
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->char('sexe', 1);
            $table->date('date_naissance');
            $table->string('lieu_naissance');
            $table->string('pays');
            $table->string('cni', 30)->index();

            // Coordonnées et parents
            $table->string('telephone', 20);
            $table->string('email')->index();
            $table->string('nom_pere')->nullable();
            $table->string('nom_mere');
            $table->string('contact_parent', 20);

            // Diplôme d'admission
            $table->string('diplome', 30);
            $table->string('option_diplome', 50)->nullable();
            $table->unsignedSmallInteger('annee_obtention')->nullable();

            $table->string('statut', 20)->default('en_attente')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inscription_choix', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('specialite_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('rang');
            $table->timestamps();

            $table->unique(['inscription_id', 'rang']);
            $table->unique(['inscription_id', 'specialite_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscription_choix');
        Schema::dropIfExists('inscriptions');
    }
};
