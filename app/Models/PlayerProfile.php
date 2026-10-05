<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'fecha_nacimiento',
        'posicion_principal',
        'posicion_secundaria',
        'pierna_habil',
        'ciudad',
        'nivel',
        'biografia',
        
        // Stats
        'rating_general',
        'ritmo',
        'disparo',
        'pase',
        'regate',
        'defensa',
        'fisico',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
