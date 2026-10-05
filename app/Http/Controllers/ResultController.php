<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\MatchResult;
use App\Models\MatchResultDetail;
use App\Models\MatchResultApproval;
use App\Models\Dispute;
use App\Traits\ApiResponse;
use App\Http\Requests\MatchResultStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    use ApiResponse;

    public function storeResult(MatchResultStoreRequest $request, $id)
    {
        $match = GameMatch::findOrFail($id);

        if ($match->estado !== 'programado') {
            return $this->errorResponse('El partido no está en estado programado.', 400);
        }

        DB::beginTransaction();
        try {
            $data = $request->validated();
            
            $result = MatchResult::create([
                'game_match_id' => $match->id,
                'reporter_id' => Auth::id(),
                'team_a_score' => $data['team_a_score'],
                'team_b_score' => $data['team_b_score'],
                'estado' => 'esperando_aprobacion'
            ]);

            if (isset($data['details'])) {
                foreach ($data['details'] as $detail) {
                    $result->details()->create($detail);
                }
            }

            $match->update(['estado' => 'esperando_aprobacion']);
            
            // Auto approve from the reporter
            $result->approvals()->create([
                'user_id' => Auth::id(),
                'status' => 'aprobado'
            ]);

            DB::commit();
            return $this->successResponse($result->load('details'), 'Resultado reportado y esperando aprobación', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error al reportar resultado: ' . $e->getMessage(), 500);
        }
    }

    public function approveResult(Request $request, $id)
    {
        $match = GameMatch::findOrFail($id);
        $result = MatchResult::where('game_match_id', $match->id)->firstOrFail();

        if ($result->estado !== 'esperando_aprobacion') {
            return $this->errorResponse('El resultado no está esperando aprobación.', 400);
        }

        // Check if user already approved
        $existing = MatchResultApproval::where('match_result_id', $result->id)->where('user_id', Auth::id())->first();
        if ($existing) {
            return $this->errorResponse('Ya has emitido tu voto para este resultado.', 400);
        }

        DB::beginTransaction();
        try {
            $result->approvals()->create([
                'user_id' => Auth::id(),
                'status' => 'aprobado'
            ]);

            // Check if approvals threshold is met (example: 3 approvals)
            $approvalsCount = $result->approvals()->where('status', 'aprobado')->count();

            if ($approvalsCount >= 3) {
                $result->update(['estado' => 'confirmado']);
                $match->update(['estado' => 'finalizado']);

                // TODO: Here we would dispatch a Job to calculate PlayerStat and TeamStat updates
                // Updating stats synchronously ...
            }

            DB::commit();
            return $this->successResponse(null, 'Resultado aprobado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error al aprobar: ' . $e->getMessage(), 500);
        }
    }

    public function raiseDispute(Request $request, $id)
    {
        $request->validate(['motivo' => 'required|string', 'evidencia_url' => 'nullable|url']);

        $match = GameMatch::findOrFail($id);
        $result = MatchResult::where('game_match_id', $match->id)->firstOrFail();

        DB::beginTransaction();
        try {
            $dispute = Dispute::create([
                'game_match_id' => $match->id,
                'match_result_id' => $result->id,
                'reporter_id' => Auth::id(),
                'motivo' => $request->motivo,
                'evidencia_url' => $request->evidencia_url,
                'estado' => 'abierto'
            ]);

            $result->update(['estado' => 'en_disputa']);
            $match->update(['estado' => 'disputa']);

            DB::commit();
            return $this->successResponse($dispute, 'Disputa generada. Un administrador revisará el caso.', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error al generar disputa: ' . $e->getMessage(), 500);
        }
    }
}
