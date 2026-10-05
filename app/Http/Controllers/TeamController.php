<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\TeamMember;
use App\Traits\ApiResponse;
use App\Http\Requests\TeamStoreRequest;
use App\Http\Requests\TeamUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $teams = Team::with(['capitan', 'teamStats'])->get();
        return $this->successResponse($teams, 'Equipos obtenidos con éxito');
    }

    public function store(TeamStoreRequest $request)
    {
        $user = Auth::user();

        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['capitan_id'] = $user->id;
            
            $team = Team::create($data);
            
            // Create stats record for team
            $team->teamStats()->create([]);

            // Add capitan as member automatically
            $team->teamMembers()->create([
                'user_id' => $user->id,
                'rol' => 'capitan',
                'estado' => 'activo',
                'joined_at' => now()
            ]);

            DB::commit();
            
            return $this->successResponse($team->load(['teamStats', 'teamMembers']), 'Equipo creado con éxito', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error al crear el equipo: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $team = Team::with(['capitan', 'teamStats', 'teamMembers.user'])->findOrFail($id);
        return $this->successResponse($team, 'Equipo obtenido con éxito');
    }

    public function update(TeamUpdateRequest $request, $id)
    {
        $team = Team::findOrFail($id);

        if ($team->capitan_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return $this->errorResponse('No tienes permiso para actualizar este equipo.', 403);
        }

        $team->update($request->validated());

        return $this->successResponse($team, 'Equipo actualizado con éxito');
    }

    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        
        if ($team->capitan_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return $this->errorResponse('No tienes permiso para eliminar este equipo.', 403);
        }

        $team->delete();
        return $this->successResponse(null, 'Equipo eliminado con éxito');
    }

    public function invite(Request $request, $id)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $team = Team::findOrFail($id);
        
        if ($team->capitan_id !== Auth::id()) {
            return $this->errorResponse('Solo el capitán puede invitar.', 403);
        }

        $member = $team->teamMembers()->firstOrCreate([
            'user_id' => $request->user_id
        ], [
            'rol' => 'jugador',
            'estado' => 'invitado'
        ]);

        return $this->successResponse($member, 'Invitación enviada');
    }

    public function join(Request $request, $id)
    {
        $team = Team::findOrFail($id);
        
        $member = $team->teamMembers()->where('user_id', Auth::id())->first();

        if ($member && $member->estado === 'invitado') {
            $member->update(['estado' => 'activo', 'joined_at' => now()]);
            return $this->successResponse($member, 'Te has unido al equipo correctamente.');
        }

        // Si es un equipo público / abierto (Lógica simplificada)
        $newMember = $team->teamMembers()->create([
            'user_id' => Auth::id(),
            'rol' => 'jugador',
            'estado' => 'activo',
            'joined_at' => now()
        ]);

        return $this->successResponse($newMember, 'Te has unido al equipo correctamente.');
    }

    public function card($id)
    {
        $team = Team::with(['teamStats'])->findOrFail($id);
        
        $card = [
            'id' => $team->id,
            'nombre' => $team->nombre,
            'logo' => $team->logo,
            'rating' => $team->rating_general,
            'stats' => [
                'ATA' => $team->ataque,
                'MED' => $team->mediocampo,
                'DEF' => $team->defensa
            ],
            'performance' => $team->teamStats
        ];

        return $this->successResponse($card, 'Carta de equipo generada');
    }
}
