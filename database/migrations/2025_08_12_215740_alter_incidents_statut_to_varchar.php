<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE incidents MODIFY statut VARCHAR(20) NOT NULL");

        DB::table('incidents')->where('statut', 'résolu')->update(['statut' => 'resolu']);
        DB::table('incidents')->where('statut', 'fermé')->update(['statut' => 'ferme']);
        DB::table('incidents')->whereNotIn('statut', ['nouveau','en_cours','resolu','ferme'])
            ->update(['statut' => 'nouveau']);
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE incidents
            MODIFY statut ENUM('nouveau','en_cours','résolu','fermé') NOT NULL");
    }
};
