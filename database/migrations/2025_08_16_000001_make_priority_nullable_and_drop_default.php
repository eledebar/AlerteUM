<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE incidents ALTER COLUMN priority DROP DEFAULT");
            DB::statement("ALTER TABLE incidents ALTER COLUMN priority DROP NOT NULL");
        } elseif (in_array($driver, ['mysql','mariadb'])) {
            DB::statement("ALTER TABLE incidents MODIFY priority VARCHAR(255) NULL DEFAULT NULL");
        }

        DB::table('incidents')
            ->whereIn('statut', ['nouveau'])
            ->whereNull('attribue_a')
            ->where('priority', 'medium')
            ->update(['priority' => null]);
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE incidents ALTER COLUMN priority SET DEFAULT 'medium'");
            DB::statement("ALTER TABLE incidents ALTER COLUMN priority SET NOT NULL");
        } elseif (in_array($driver, ['mysql','mariadb'])) {
            DB::statement("ALTER TABLE incidents MODIFY priority VARCHAR(255) NOT NULL DEFAULT 'medium'");
        }
    }
};
