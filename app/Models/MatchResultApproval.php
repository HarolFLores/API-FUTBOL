<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchResultApproval extends Model
{
    protected $fillable = ['match_result_id', 'user_id', 'status', 'comentarios'];

    public function matchResult() { return $this->belongsTo(MatchResult::class); }
    public function user() { return $this->belongsTo(User::class); }
}
