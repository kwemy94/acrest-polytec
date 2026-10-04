<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained()->cascadeOnDelete();
            $table->string('operateur', 20);
            $table->unsignedInteger('montant');
            $table->string('telephone', 20);
            $table->string('reference', 50)->unique();
            $table->string('statut', 20)->default('en_attente')->index();
            $table->text('note')->nullable();
            $table->timestamp('traite_le')->nullable();
            $table->foreignId('traite_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
