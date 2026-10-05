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
        Schema::create('match_participants', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('game_match_id');
            $table->foreign('game_match_id')->references('id')->on('game_matches')->cascadeOnDelete();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unsignedInteger('team_id')->nullable();
            $table->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->string('side')->nullable(); // team_a, team_b (useful for free matches)
            $table->string('asistencia_estado')->default('pendiente'); // pendiente, confirmado, rechazado
            $table->boolean('participo')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_participants');
    }
};
