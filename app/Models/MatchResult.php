<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchResult extends Model
{
    protected $fillable = ['game_match_id', 'reporter_id', 'team_a_score', 'team_b_score', 'estado'];

    public function gameMatch() { return $this->belongsTo(GameMatch::class); }
    public function reporter() { return $this->belongsTo(User::class, 'reporter_id'); }
    public function details() { return $this->hasMany(MatchResultDetail::class); }
    public function approvals() { return $this->hasMany(MatchResultApproval::class); }
    public function disputes() { return $this->hasMany(Dispute::class); }
}
