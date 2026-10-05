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
        Schema::create('match_result_approvals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('match_result_id');
            $table->foreign('match_result_id')->references('id')->on('match_results')->cascadeOnDelete();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('side'); // team_a, team_b
            $table->string('decision'); // aprobado, rechazado
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_result_approvals');
    }
};
