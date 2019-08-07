<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewGame
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $oppoUserId;
    public $gameId;
    public $curr_user;

    public function __construct($oppoUserId, $gameId,$curr_user)
    {
        $this->oppoUserId = $oppoUserId;
        $this->gameId = $gameId;
        $this->curr_user = $curr_user;
        //$this->$this->broadcastToEveryone();
    }

    public function broadcastOn()
    {
        return new PrivateChannel('new-game-channel-'.$this->gameId);
    }

}
