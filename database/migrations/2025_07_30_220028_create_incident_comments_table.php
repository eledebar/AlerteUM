<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
  public function up()
{
    Schema::create('incident_comments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('incident_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->text('commentaire');
        $table->timestamps();
    });
}


    public function down(): void
    {
        Schema::dropIfExists('incident_comments');
    }
};
