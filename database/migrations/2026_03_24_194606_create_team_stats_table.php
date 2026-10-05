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
        Schema::create('team_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id');
            $table->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->unsignedSmallInteger('partidos')->default(0);
            $table->unsignedSmallInteger('victorias')->default(0);
            $table->unsignedSmallInteger('empates')->default(0);
            $table->unsignedSmallInteger('derrotas')->default(0);
            $table->unsignedSmallInteger('goles_favor')->default(0);
            $table->unsignedSmallInteger('goles_contra')->default(0);
            $table->smallInteger('diferencia_goles')->default(0);
            $table->unsignedSmallInteger('reputacion')->default(100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_stats');
    }
};
