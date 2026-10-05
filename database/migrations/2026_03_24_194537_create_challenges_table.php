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
        Schema::create('challenges', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('challenger_team_id');
            $table->foreign('challenger_team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->unsignedInteger('challenged_team_id')->nullable();
            $table->foreign('challenged_team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->string('tipo_reto')->default('directo');
            $table->date('fecha_propuesta');
            $table->time('hora_propuesta');
            $table->string('ubicacion')->nullable();
            $table->string('formato')->nullable();
            $table->string('nivel_sugerido')->nullable();
            $table->decimal('costo_compartido', 8, 2)->default(0);
            $table->string('estado')->default('publicado');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
