<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\GameMatch;
use App\Traits\ApiResponse;
use App\Http\Requests\ChallengeStoreRequest;
use App\Http\Requests\ChallengeUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChallengeController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $challenges = Challenge::with(['challengerTeam', 'challengedTeam'])->get();
        return $this->successResponse($challenges, 'Retos obtenidos con éxito');
    }

    public function store(ChallengeStoreRequest $request)
    {
        $data = $request->validated();
        
        $challenge = Challenge::create($data);

        return $this->successResponse($challenge->load('challengerTeam', 'challengedTeam'), 'Reto creado con éxito', 201);
    }

    public function show($id)
    {
        $challenge = Challenge::with(['challengerTeam', 'challengedTeam'])->findOrFail($id);
        return $this->successResponse($challenge, 'Reto obtenido con éxito');
    }

    public function update(ChallengeUpdateRequest $request, $id)
    {
        $challenge = Challenge::findOrFail($id);
        $challenge->update($request->validated());
        return $this->successResponse($challenge, 'Reto actualizado con éxito');
    }

    public function destroy($id)
    {
        $challenge = Challenge::findOrFail($id);
        $challenge->delete();
        return $this->successResponse(null, 'Reto eliminado con éxito');
    }

    public function accept($id)
    {
        $challenge = Challenge::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $challenge->update(['estado' => 'aceptado']);
            
            // Generate the match
            $match = GameMatch::create([
                'match_type' => 'equipo',
                'challenge_id' => $challenge->id,
                'team_a_id' => $challenge->challenger_team_id,
                'team_b_id' => $challenge->challenged_team_id,
                'fecha' => $challenge->fecha_propuesta,
                'hora' => $challenge->hora_propuesta,
                'ubicacion' => $challenge->ubicacion,
                'formato' => $challenge->formato,
                'costo' => $challenge->costo_compartido,
                'estado' => 'programado',
                'observaciones' => 'Creado a partir del Reto #' . $challenge->id
            ]);
            DB::commit();

            return $this->successResponse($match, 'Reto aceptado y partido programado', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Ocurrió un error al aceptar el reto', 500);
        }
    }

    public function reject($id)
    {
        $challenge = Challenge::findOrFail($id);
        $challenge->update(['estado' => 'rechazado']);
        return $this->successResponse($challenge, 'Reto rechazado');
    }

    public function counter(Request $request, $id)
    {
        $request->validate([
            'fecha_propuesta' => 'required|date|after_or_equal:today',
            'hora_propuesta' => 'required|date_format:H:i',
            'ubicacion' => 'nullable|string|max:255'
        ]);

        $challenge = Challenge::findOrFail($id);
        $challenge->update([
            'fecha_propuesta' => $request->fecha_propuesta,
            'hora_propuesta' => $request->hora_propuesta,
            'ubicacion' => $request->ubicacion,
            'estado' => 'contraoferta'
        ]);

        return $this->successResponse($challenge, 'Contraoferta enviada');
    }
}
