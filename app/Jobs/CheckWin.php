<?php

namespace App\Jobs;

use App\Turn;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckWin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $gameId;
    public function __construct($gameId)
    {
        //
        $this->gameId=$gameId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $win = array
        (
            array("1","2","3"),
            array("4","5","6"),
            array("7","8","9"),
            array("1","4","7"),
            array("2","5","8"),
            array("3","6","9"),
            array("1","5","9"),
            array("3","5","7"),
        );
        for ($row = 0; $row < 7; $row++) {
            $col = 0;
            $pos1x=(string)Turn::where('game_id', '=', $this->gameId)->where('location','=',$win[$row][$col])->where('type','=','x')->select('location');
            $pos2x=(string)Turn::where('game_id', '=', $this->gameId)->where('location','=',$win[$row][$col+1])->where('type','=','x')->select('location');
            $pos3x=(string)Turn::where('game_id', '=', $this->gameId)->where('location','=',$win[$row][$col+2])->where('type','=','x')->select('location');
            $pos1o=(string)Turn::where('game_id', '=', $this->gameId)->where('location','=',$win[$row][$col])->where('type','=','o')->select('location');
            $pos2o=(string)Turn::where('game_id', '=', $this->gameId)->where('location','=',$win[$row][$col+1])->where('type','=','o')->select('location');
            $pos3o=(string)Turn::where('game_id', '=', $this->gameId)->where('location','=',$win[$row][$col+2])->where('type','=','o')->select('location');
            if(($pos1x&&$pos2x&&$pos3x)||($pos1o&&$pos2o&&$pos3o)) {
                if ($pos1x = $win[$row][$col] && $pos2x = $win[$row][$col + 1] && $pos3x = $win[$row][$col + 2]) {
                    $winner_id = Turn::where('game_id', '=', $this->gameId)->where('type', '=', 'x')->select('player_id');
                    $loser_id = Turn::where('game_id', '=', $this->gameId)->where('type', '=', 'o')->select('player_id');
                    return [$winner_id, $loser_id];

                } elseif ($pos1o = $win[$row][$col] && $pos2o = $win[$row][$col + 1] && $pos3o = $win[$row][$col + 2]) {
                    $winner_id = Turn::where('game_id', '=', $this->gameId)->where('type', '=', 'x')->select('player_id');
                    $loser_id = Turn::where('game_id', '=', $this->gameId)->where('type', '=', 'o')->select('player_id');
                    return [$winner_id, $loser_id];
                }
                else return null;
            }
        }
    }

    }
