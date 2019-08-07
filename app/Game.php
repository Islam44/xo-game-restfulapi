<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['winner_id','loser_id'];
    public  function  turns()
    {
        $this->hasMany(Turn::class);

    }

}
