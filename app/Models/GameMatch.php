<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameMatch extends Model
{
    protected $fillable = [
        'match_type', 'challenge_id', 'organizer_id', 
        'team_a_id', 'team_b_id', 'fecha', 'hora', 
        'ubicacion', 'formato', 'costo', 'estado', 'observaciones'
    ];

    public function teamA()
    {
        return $this->belongsTo(Team::class, 'team_a_id');
    }

    public function teamB()
    {
        return $this->belongsTo(Team::class, 'team_b_id');
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function challenge()
    {
        return $this->belongsTo(Challenge::class, 'challenge_id');
    }

    public function participants()
    {
        return $this->hasMany(MatchParticipant::class);
    }
    
    public function freeRegistrations()
    {
        return $this->hasMany(FreeMatchRegistration::class);
    }
}
