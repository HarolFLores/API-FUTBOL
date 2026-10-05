<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    protected $fillable = ['game_match_id', 'match_result_id', 'reporter_id', 'motivo', 'evidencia_url', 'estado', 'resolucion'];

    public function gameMatch() { return $this->belongsTo(GameMatch::class); }
    public function matchResult() { return $this->belongsTo(MatchResult::class); }
    public function reporter() { return $this->belongsTo(User::class, 'reporter_id'); }
}
