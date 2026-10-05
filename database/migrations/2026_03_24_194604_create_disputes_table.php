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
        Schema::create('disputes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('game_match_id');
            $table->foreign('game_match_id')->references('id')->on('game_matches')->cascadeOnDelete();
            $table->unsignedInteger('match_result_id');
            $table->foreign('match_result_id')->references('id')->on('match_results')->cascadeOnDelete();
            $table->unsignedInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            $table->text('motivo');
            $table->string('estado')->default('abierta'); // abierta, resuelta
            $table->unsignedInteger('resolved_by')->nullable();
            $table->foreign('resolved_by')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
