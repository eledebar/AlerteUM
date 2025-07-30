<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->enum('statut', ['nouveau', 'en_cours', 'résolu'])->default('nouveau');
            
            // L'utilisateur qui a signalé l'incident
            $table->foreignId('utilisateur_id')->constrained('users')->onDelete('cascade');
            
            // L'utilisateur à qui l'incident est attribué (peut être null au début)
            $table->foreignId('attribue_a')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
