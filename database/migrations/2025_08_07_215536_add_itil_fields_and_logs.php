<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('incident_logs')) {
            Schema::create('incident_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('incident_id')->constrained('incidents')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action');
                $table->string('from_status')->nullable();
                $table->string('to_status')->nullable();
                $table->text('details')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('incidents', function (Blueprint $table) {
            if (!Schema::hasColumn('incidents', 'public_id')) {
                $table->string('public_id')->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('incidents', 'priority')) {
                $table->string('priority')->default('medium')->after('statut');
            }
            if (!Schema::hasColumn('incidents', 'escalation_level')) {
                $table->unsignedTinyInteger('escalation_level')->default(0)->after('priority');
            }
            if (!Schema::hasColumn('incidents', 'sla_due_at')) {
                $table->timestamp('sla_due_at')->nullable()->after('escalation_level');
            }
            if (!Schema::hasColumn('incidents', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('sla_due_at');
            }
            if (!Schema::hasColumn('incidents', 'closed_at')) {
                $table->timestamp('closed_at')->nullable()->after('resolved_at');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_logs');
        Schema::table('incidents', function (Blueprint $table) {
            foreach (['public_id','priority','escalation_level','sla_due_at','resolved_at','closed_at'] as $col) {
                if (Schema::hasColumn('incidents', $col)) $table->dropColumn($col);
            }
        });
    }
};
