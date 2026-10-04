<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('langue', 10)->default('fr')->after('type')->index();

            // Version numérique (PDF stocké sur le disque privé)
            $table->string('fichier')->nullable()->after('consultation_sur_place');
            $table->unsignedInteger('fichier_taille')->nullable()->after('fichier');
            $table->boolean('telechargeable')->default(false)->after('fichier_taille');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['langue']);
            $table->dropColumn(['langue', 'fichier', 'fichier_taille', 'telechargeable']);
        });
    }
};
