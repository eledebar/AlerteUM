<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->string('statut', 20)->default('nouveau')->change();
        });

        DB::table('incidents')->where('statut', 'résolu')->update(['statut' => 'resolu']);
        DB::table('incidents')->where('statut', 'fermé')->update(['statut' => 'ferme']);
        DB::table('incidents')->where('statut', 'en cours')->update(['statut' => 'en_cours']);

        Schema::table('incidents', function (Blueprint $table) {
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->string('statut', 20)->default('nouveau')->change();
        });
    }
};
