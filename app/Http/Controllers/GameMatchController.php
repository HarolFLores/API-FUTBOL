<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\MatchParticipant;
use App\Models\FreeMatchRegistration;
use App\Traits\ApiResponse;
use App\Http\Requests\FreeMatchStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GameMatchController extends Controller
{
    use ApiResponse;

    // --- Matches (Teams) ---

    public function index()
    {
        $matches = GameMatch::where('match_type', 'equipo')->with(['teamA', 'teamB'])->get();
        return $this->successResponse($matches, 'Partidos obtenidos con éxito');
    }

    public function show($id)
    {
        $match = GameMatch::with(['teamA', 'teamB', 'participants.user'])->findOrFail($id);
        return $this->successResponse($match, 'Partido obtenido con éxito');
    }

    public function confirmAttendance(Request $request, $id)
    {
        $match = GameMatch::findOrFail($id);
        $user = Auth::user();

        // Check if user is already participating
        $participant = MatchParticipant::firstOrCreate(
            ['game_match_id' => $match->id, 'user_id' => $user->id],
            ['asistencia_estado' => 'confirmado']
        );

        if (!$participant->wasRecentlyCreated) {
            $participant->update(['asistencia_estado' => 'confirmado']);
        }

        return $this->successResponse($participant, 'Asistencia confirmada.');
    }

    // --- Free Matches ---
    
    public function freeIndex()
    {
        $matches = GameMatch::where('match_type', 'libre')->with(['organizer', 'freeRegistrations.user'])->get();
        return $this->successResponse($matches, 'Encuentros libres obtenidos con éxito');
    }

    public function freeStore(FreeMatchStoreRequest $request)
    {
        $data = $request->validated();
        $data['match_type'] = 'libre';
        $data['organizer_id'] = Auth::id();
        $data['estado'] = 'abierto';

        $match = GameMatch::create($data);

        return $this->successResponse($match, 'Encuentro libre creado', 201);
    }

    public function freeShow($id)
    {
        $match = GameMatch::where('match_type', 'libre')->with(['organizer', 'freeRegistrations.user'])->findOrFail($id);
        return $this->successResponse($match, 'Encuentro libre obtenido');
    }

    public function joinFreeMatch($id)
    {
        $match = GameMatch::where('match_type', 'libre')->findOrFail($id);

        $registration = FreeMatchRegistration::firstOrCreate([
            'game_match_id' => $match->id,
            'user_id' => Auth::id()
        ], [
            'estado_inscripcion' => 'inscrito',
            'confirmado' => true
        ]);

        return $this->successResponse($registration, 'Inscrito en el encuentro libre.');
    }

    public function leaveFreeMatch($id)
    {
        $match = GameMatch::where('match_type', 'libre')->findOrFail($id);
        
        $registration = FreeMatchRegistration::where('game_match_id', $match->id)
            ->where('user_id', Auth::id())->first();

        if ($registration) {
            $registration->delete();
            return $this->successResponse(null, 'Te has dado de baja del encuentro libre.');
        }

        return $this->errorResponse('No estabas inscrito.', 400);
    }

    public function assignTeams(Request $request, $id)
    {
        $match = GameMatch::where('match_type', 'libre')->findOrFail($id);
        $organizer_id = $match->organizer_id;

        if ($organizer_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return $this->errorResponse('Solo el organizador puede asignar los equipos.', 403);
        }

        $request->validate([
            'assignments' => 'required|array',
            'assignments.*.user_id' => 'required|exists:users,id',
            'assignments.*.side' => 'required|in:team_a,team_b' // side assigns random players
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->assignments as $assignment) {
                MatchParticipant::updateOrCreate([
                    'game_match_id' => $match->id,
                    'user_id' => $assignment['user_id']
                ], [
                    'side' => $assignment['side'],
                    'asistencia_estado' => 'confirmado'
                ]);
            }
            
            $match->update(['estado' => 'programado']);

            DB::commit();
            return $this->successResponse(null, 'Equipos aleatorios/libres asignados.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error al asignar equipos: ' . $e->getMessage(), 500);
        }
    }
}
