<?php

namespace App\Http\Controllers;

use App\Events\GameOver;
use App\Events\Play;
use App\Game;
use App\Jobs\CheckWin;
use App\Turn;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    public function play(Request $request, $gameId)
    {
        $curr_user = auth()->user();
        $location = $request->get('location');

        $turn = Turn::where('game_id', '=', $gameId)->whereNull('location')->orderBy('id')->first();
        $turn->location = $location;
        $turn->save();
        $this->dispatch(new  CheckWin($gameId));
        event(new Play($gameId, $turn->type, $location, $curr_user->id));
        return response()->json(["status" => "success", "data" => "Saved"]);
    }

    public function gameOver(Request $request,$gameId,$winner_id,$loser_id){
        $curr_user = auth()->user();
        $location = $request->get('location');

        $turn = Turn::where('game_id', '=', $gameId)->whereNull('location')->orderBy('id')->first();
        $turn->location = $location;
        $turn->save();
        event(new GameOver($gameId, $curr_user->id, $request->get('result'), $location, $turn->type));
        DB::transaction(function() use ($gameId,$winner_id,$loser_id) {
            Game::where('id', $gameId)
                ->update(['winner_id' => $winner_id,'loser_id' =>$loser_id]);
            User::where('id',$winner_id)->increment('total_win');
            User::where('id',$loser_id)->decrement('total_lose');
            $total_win=(int) User::where('id', $winner_id)->select('total_win');
            $total_lose=(int) User::where('id', $winner_id)->select('total_lose');
            User::where('id', $winner_id)
                ->update(['score' =>($total_win+$total_lose)*100].' %');
            /////////////////////////////////////////////////////
            $total_win=(int) User::where('id', $loser_id)->select('total_win');
            $total_lose=(int) User::where('id', $loser_id)->select('total_lose');
            User::where('id', $loser_id)
                ->update(['score' =>($total_win+$total_lose)*100]);
                User::where('id','=',$winner_id)->update(['play_mode'=>false]);
                User::where('id','=',$loser_id)->update(['play_mode'=>false]);
            ////////////////////////////////////////////////////////
            Turn::where('game_id', '=', $gameId)->delete();

        });

        return response()->json(["status" => "success", "data" => "Saved Game over"]);
    }
    public function check_winner()
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
            array("2","3","4")
        );
        for ($row = 0; $row < $win.count(); $row++) {
            $col = 0;
            $pos1x=(string)$win[$row][$col];
            $pos2x=(string)$win[$row][$col+1];
            $pos3x=(string)$win[$row][$col+2];
            $pos1o=(string)$win[$row][$col];
            $pos2o=(string)$win[$row][$col+1];
            $pos3o=(string)$win[$row][$col+2];
            if(($pos1x&&$pos2x&&$pos3x)||($pos1o&&$pos2o&&$pos3o)) {
                if ($pos1x = $win[$row][$col] && $pos2x = $win[$row][$col + 1] && $pos3x = $win[$row][$col + 2]) {
                    echo 'win x';

                } elseif ($pos1o = $win[$row][$col] && $pos2o = $win[$row][$col + 1] && $pos3o = $win[$row][$col + 2]) {

                    echo 'win y';
                    ;                }
                else echo 'continue';
            }
        }
    }




}
