<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    protected $fillable = [
        'challenger_team_id', 'challenged_team_id', 'tipo_reto', 
        'fecha_propuesta', 'hora_propuesta', 'ubicacion', 
        'formato', 'nivel_sugerido', 'costo_compartido', 
        'estado', 'observaciones'
    ];

    public function challengerTeam()
    {
        return $this->belongsTo(Team::class, 'challenger_team_id');
    }

    public function challengedTeam()
    {
        return $this->belongsTo(Team::class, 'challenged_team_id');
    }
}
