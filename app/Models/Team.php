<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'nombre', 'logo', 'ciudad', 'descripcion', 'capitan_id', 
        'formato_preferido', 'rating_general', 'ataque', 'mediocampo', 
        'defensa', 'reputacion', 'estado'
    ];

    public function capitan()
    {
        return $this->belongsTo(User::class, 'capitan_id');
    }

    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class);
    }

    public function teamStats()
    {
        return $this->hasOne(TeamStat::class);
    }
}
