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
        Schema::create('player_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('partidos')->default(0);
            $table->unsignedSmallInteger('victorias')->default(0);
            $table->unsignedSmallInteger('empates')->default(0);
            $table->unsignedSmallInteger('derrotas')->default(0);
            $table->unsignedSmallInteger('goles')->default(0);
            $table->unsignedSmallInteger('asistencias')->default(0);
            $table->unsignedSmallInteger('mvp')->default(0);
            $table->unsignedSmallInteger('amarillas')->default(0);
            $table->unsignedSmallInteger('rojas')->default(0);
            $table->unsignedSmallInteger('reputacion')->default(100);
            $table->decimal('asistencia_pct', 5, 2)->default(100.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_stats');
    }
};
