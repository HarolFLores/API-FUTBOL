<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchResultDetail extends Model
{
    protected $fillable = ['match_result_id', 'user_id', 'team_id', 'goles', 'asistencias', 'tarjetas_amarillas', 'tarjeta_roja', 'mvp'];

    public function matchResult() { return $this->belongsTo(MatchResult::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function team() { return $this->belongsTo(Team::class); }
}
