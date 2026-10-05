<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreeMatchRegistration extends Model
{
    protected $fillable = [
        'game_match_id', 'user_id', 'estado_inscripcion', 'fecha_inscripcion', 'confirmado'
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
