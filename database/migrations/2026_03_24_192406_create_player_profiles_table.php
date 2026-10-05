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
        Schema::create('player_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('posicion_principal')->nullable();
            $table->string('posicion_secundaria')->nullable();
            $table->string('pierna_habil')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('nivel')->nullable();
            $table->text('biografia')->nullable();
            
            // Stats
            $table->unsignedTinyInteger('rating_general')->default(50);
            $table->unsignedTinyInteger('ritmo')->default(50);
            $table->unsignedTinyInteger('disparo')->default(50);
            $table->unsignedTinyInteger('pase')->default(50);
            $table->unsignedTinyInteger('regate')->default(50);
            $table->unsignedTinyInteger('defensa')->default(50);
            $table->unsignedTinyInteger('fisico')->default(50);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_profiles');
    }
};
