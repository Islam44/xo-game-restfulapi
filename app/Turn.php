<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Turn extends Model
{
   public $fillable = ['player_id', 'location', 'type', 'game_id'];
    protected $guarded = [];
    public function game() {
        $this->belongsTo(Game::class);
    }
}
