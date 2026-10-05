<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('match_results', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('game_match_id');
            $table->foreign('game_match_id')->references('id')->on('game_matches')->cascadeOnDelete();
            $table->unsignedTinyInteger('score_team_a')->default(0);
            $table->unsignedTinyInteger('score_team_b')->default(0);
            $table->unsignedInteger('submitted_by');
            $table->foreign('submitted_by')->references('id')->on('users')->cascadeOnDelete();
            $table->string('status')->default('borrador'); // borrador, pendiente_aprobacion, aprobado, observado, rechazado, en_disputa
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_results');
    }
};
