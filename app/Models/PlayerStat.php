<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerStat extends Model
{
    protected $fillable = ['user_id', 'partidos_jugados', 'partidos_ganados', 'partidos_empatados', 'partidos_perdidos', 'goles', 'asistencias', 'mvps'];

    public function user() { return $this->belongsTo(User::class); }
}
