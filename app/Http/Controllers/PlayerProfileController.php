<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Http\Requests\PlayerProfileUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PlayerProfileController extends Controller
{
    use ApiResponse;

    // Get my logged-in profile
    public function myProfile()
    {
        $user = Auth::user()->load(['playerProfile', 'playerStats']);
        return $this->successResponse($user, 'Perfil cargado con éxito');
    }

    // Update my profile
    public function updateProfile(PlayerProfileUpdateRequest $request)
    {
        $user = Auth::user();
        
        $user->update($request->only(['nombre', 'telefono', 'foto']));
        
        if ($request->has('posicion_principal')) {
            $user->playerProfile->update(['posicion_principal' => $request->posicion_principal]);
        }
        
        return $this->successResponse($user->load('playerProfile'), 'Perfil actualizado con éxito');
    }

    // View specific player profile
    public function show($id)
    {
        $user = User::with(['playerProfile', 'playerStats'])->findOrFail($id);
        return $this->successResponse($user, 'Perfil obtenido con éxito');
    }

    // View player FIFA-style card
    public function card($id)
    {
        $user = User::with(['playerProfile', 'playerStats'])->findOrFail($id);
        
        // Assemble card logic
        $card = [
            'id' => $user->id,
            'nombre' => $user->nombre,
            'foto' => $user->foto,
            'posicion' => $user->playerProfile->posicion_principal ?? 'N/A',
            'rating' => $user->playerProfile->rating_general,
            'stats' => [
                'PAC' => $user->playerProfile->ritmo,
                'SHO' => $user->playerProfile->disparo,
                'PAS' => $user->playerProfile->pase,
                'DRI' => $user->playerProfile->regate,
                'DEF' => $user->playerProfile->defensa,
                'PHY' => $user->playerProfile->fisico,
            ],
            'performance' => $user->playerStats
        ];
        
        return $this->successResponse($card, 'Carta de jugador generada');
    }

    // Optional ranking method
    public function ranking()
    {
        // Simple ranking logic for top 10 players based on rating
        $players = User::with(['playerProfile'])
            ->whereHas('roles', function($q) {
                $q->where('name', 'jugador');
            })
            ->join('player_profiles', 'users.id', '=', 'player_profiles.user_id')
            ->orderByDesc('player_profiles.rating_general')
            ->select('users.*')
            ->take(10)
            ->get();
            
        return $this->successResponse($players, 'Ranking de jugadores');
    }
}
