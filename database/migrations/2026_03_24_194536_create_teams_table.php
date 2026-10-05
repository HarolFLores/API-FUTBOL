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
        Schema::create('teams', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->string('logo')->nullable();
            $table->string('ciudad')->nullable();
            $table->text('descripcion')->nullable();
            $table->unsignedInteger('capitan_id');
            $table->foreign('capitan_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('formato_preferido')->nullable();
            $table->unsignedTinyInteger('rating_general')->default(50);
            $table->unsignedTinyInteger('ataque')->default(50);
            $table->unsignedTinyInteger('mediocampo')->default(50);
            $table->unsignedTinyInteger('defensa')->default(50);
            $table->unsignedSmallInteger('reputacion')->default(100);
            $table->string('estado')->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
