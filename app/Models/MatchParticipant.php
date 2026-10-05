<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchParticipant extends Model
{
    protected $fillable = [
        'game_match_id', 'user_id', 'team_id', 'side', 'asistencia_estado', 'participo'
    ];

    public function gameMatch()
    {
        return $this->belongsTo(GameMatch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
