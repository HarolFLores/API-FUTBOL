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
        Schema::create('free_match_registrations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('game_match_id');
            $table->foreign('game_match_id')->references('id')->on('game_matches')->cascadeOnDelete();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('estado_inscripcion')->default('inscrito'); // inscrito, lista_espera, cancelado
            $table->timestamp('fecha_inscripcion')->useCurrent();
            $table->boolean('confirmado')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('free_match_registrations');
    }
};
