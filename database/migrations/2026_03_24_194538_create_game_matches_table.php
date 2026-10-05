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
        Schema::create('game_matches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('match_type')->default('equipo'); // equipo, libre
            $table->unsignedInteger('challenge_id')->nullable();
            $table->foreign('challenge_id')->references('id')->on('challenges')->cascadeOnDelete();
            $table->unsignedInteger('organizer_id')->nullable();
            $table->foreign('organizer_id')->references('id')->on('users')->cascadeOnDelete(); // for free matches
            $table->unsignedInteger('team_a_id')->nullable();
            $table->foreign('team_a_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->unsignedInteger('team_b_id')->nullable();
            $table->foreign('team_b_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->date('fecha');
            $table->time('hora');
            $table->string('ubicacion')->nullable();
            $table->string('formato')->nullable();
            $table->decimal('costo', 8, 2)->default(0);
            $table->string('estado')->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_matches');
    }
};
