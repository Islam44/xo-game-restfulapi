<?php

namespace App\Http\Controllers;

use App\Events\NewGame;
use App\Notifications\UserChallenged;
use App\User;
use Illuminate\Http\Request;
use App\Game;
use  App\Turn;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $curr_user = auth()->user();
        $users = User::where('id', '!=', $curr_user->id)->where('play_mode','=',false)->get();
        return response()->json($users);

    }

    public function invite(User $user)
    {
        $curr_user = auth()->user();//you
        if ($curr_user->id == $user->id) {
            return response()->json("You can't invite yourself");
        }
        if (!$curr_user->isInviting($user->id)) {
            $curr_user->send_invite($user->id);

            // sending a notification
            $user->notify(new UserChallenged($curr_user));
            return response()->json("You Send Challenge To  {$user->name}");
        }
        return response()->json("You are already sending Challenge To  {$user->name}");
    }

    public function cancel_invite(User $user)
    {
        $curr_user = auth()->user();//you
        if ($curr_user->isInviting($user->id)) {
            $curr_user->cancel_invite($user->id);
            return response()->json("You cancel invitation to  {$user->name}");
        }
    }
        public function Reject_invite(User $user)
    {
        $curr_user = auth()->user();//you
        $curr_user->cancel_invite($user->id);
        return response()->json("You reject invitation from  {$user->name}");
    }

    public function notifications()
    {
        return auth()->user()->unreadNotifications()->get()->toArray();
    }

    public function new_game()
    {
            $curr_user = auth()->user();
            $notification = $curr_user->unreadNotifications[0];
            auth()->user()->unreadNotifications->where('id', $notification->id)->markAsRead();
            $opponentId=$notification->data['challenger_id'];
            DB::transaction(function () use($curr_user,$opponentId){
                User::where('id','=',$curr_user->id)->update(['play_mode'=>true]);
                User::where('id','=',$opponentId)->update(['play_mode'=>true]);
            });

             $gameId = Game::insertGetId([]);
             for($i = 1; $i <= 9; $i++) {
            Turn::insert([
                "game_id" => $gameId,
                "id" => $i,
                "type" => $i % 2 ? 'x' : 'o',
                "player_id" => $i % 2 ? $opponentId : $curr_user->id,
            ]);
        }
        $players = Turn::where('game_id', '=', $gameId)->select('player_id', 'type')->distinct()->get();
        $playerType = $curr_user->id == $players[0]->player_id ? $players[0]->type : $players[1]->type;
        $otherPlayerId = $curr_user->id == $players[0]->player_id ? $players[1]->player_id : $players[0]->player_id;
        $pastTurns = Turn::where('game_id', '=', $gameId)->whereNotNull('location')->orderBy('id')->get();
        $nextTurn = Turn::where('game_id', '=', $gameId)->whereNull('location')->orderBy('id')->first();
        $locations = [
            1 => [
                "class" => "top left",
                "checked" => false,
                "type" => ""
            ],
            2 => [
                "class" => "top middle",
                "checked" => false,
                "type" => ""
            ],
            3 => [
                "class" => "top right",
                "checked" => false,
                "type" => ""
            ],
            4 => [
                "class" => "center left",
                "checked" => false,
                "type" => ""
            ],
            5 => [
                "class" => "center middle",
                "checked" => false,
                "type" => ""
            ],
            6 => [
                "class" => "center right",
                "checked" => false,
                "type" => ""
            ],
            7 => [
                "class" => "bottom left",
                "checked" => false,
                "type" => ""
            ],
            8 => [
                "class" => "bottom middle",
                "checked" => false,
                "type" => ""
            ],
            9 => [   //return redirect()->action('HomeController@index');
                "class" => "bottom right",
                "checked" => false,
                "type" => ""
            ]
        ];

        foreach ($pastTurns as $pastTurn){
            $locations[$pastTurn->location]["checked"]=true;//[1][checked]
            $locations[$pastTurn->location]["type"]=$pastTurn->type;//[1][type];
        }
        broadcast(new NewGame($opponentId, $gameId, $curr_user));

        return response()->json([
            'curr_user ' =>$curr_user
            ,'gameId' =>$gameId,
            'nextTurn' =>$nextTurn,
            'locations' =>$locations,
            'playerType' =>$playerType,
            'otherPlayerId'=>$otherPlayerId,
            'check_player_turn'=>$curr_user->id == $nextTurn->player_id ?"You are next" : "Waiting on your opponent..." ,
            ]);

     //   broadcast(new NewGame($opponentId, $gameId, $curr_user));
      //  return redirect("/board/{$gameId}");
        //return redirect()->action('HomeController@index');


    }

}
