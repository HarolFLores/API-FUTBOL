<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');

    // Protected Auth routes
    Route::middleware('auth:api')->group(function() {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

// Protected routes for the rest of the application
Route::middleware('auth:api')->group(function () {
    
    // Players / Profiles
    Route::get('profile', [\App\Http\Controllers\PlayerProfileController::class, 'myProfile']);
    Route::put('profile', [\App\Http\Controllers\PlayerProfileController::class, 'updateProfile']);
    Route::get('players/{id}', [\App\Http\Controllers\PlayerProfileController::class, 'show']);
    Route::get('players/{id}/card', [\App\Http\Controllers\PlayerProfileController::class, 'card']);

    // Teams
    Route::apiResource('teams', \App\Http\Controllers\TeamController::class);
    Route::post('teams/{id}/invite', [\App\Http\Controllers\TeamController::class, 'invite']);
    Route::post('teams/{id}/join', [\App\Http\Controllers\TeamController::class, 'join']);
    Route::get('teams/{id}/card', [\App\Http\Controllers\TeamController::class, 'card']);

    // Challenges
    Route::apiResource('challenges', \App\Http\Controllers\ChallengeController::class);
    Route::post('challenges/{id}/accept', [\App\Http\Controllers\ChallengeController::class, 'accept']);
    Route::post('challenges/{id}/reject', [\App\Http\Controllers\ChallengeController::class, 'reject']);
    Route::post('challenges/{id}/counter', [\App\Http\Controllers\ChallengeController::class, 'counter']);

    // Matches (GameMatches)
    Route::apiResource('matches', \App\Http\Controllers\GameMatchController::class);
    Route::post('matches/{id}/confirm-attendance', [\App\Http\Controllers\GameMatchController::class, 'confirmAttendance']);

    // Free Matches
    Route::get('free-matches', [\App\Http\Controllers\GameMatchController::class, 'freeIndex']);
    Route::post('free-matches', [\App\Http\Controllers\GameMatchController::class, 'freeStore']);
    Route::get('free-matches/{id}', [\App\Http\Controllers\GameMatchController::class, 'freeShow']);
    Route::post('free-matches/{id}/join', [\App\Http\Controllers\GameMatchController::class, 'joinFreeMatch']);
    Route::post('free-matches/{id}/leave', [\App\Http\Controllers\GameMatchController::class, 'leaveFreeMatch']);
    Route::post('free-matches/{id}/assign-teams', [\App\Http\Controllers\GameMatchController::class, 'assignTeams']);

    // Match Results & Validations
    Route::post('matches/{id}/result', [\App\Http\Controllers\MatchResultController::class, 'store']);
    Route::get('matches/{id}/result', [\App\Http\Controllers\MatchResultController::class, 'showResult']);
    Route::post('results/{id}/approve', [\App\Http\Controllers\MatchResultController::class, 'approve']);
    Route::post('results/{id}/reject', [\App\Http\Controllers\MatchResultController::class, 'reject']);
    Route::get('results/{id}/approvals', [\App\Http\Controllers\MatchResultController::class, 'approvals']);

    // Rankings
    Route::get('rankings/players', [\App\Http\Controllers\PlayerProfileController::class, 'ranking']);
    Route::get('rankings/teams', [\App\Http\Controllers\TeamController::class, 'ranking']);

    // Notifications
    Route::get('notifications', [\App\Http\Controllers\NotificationController::class, 'index']);
    Route::post('notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);
    
    // Disputes
    Route::apiResource('disputes', \App\Http\Controllers\DisputeController::class);
});
