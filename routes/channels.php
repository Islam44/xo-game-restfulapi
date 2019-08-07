<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('new-game-channel-'.$this->gameId, function ($user) {
    return auth()->check();
});
Broadcast::channel('game-channel-' . $this->gameId . '-' . $this->userId, function ($user) {
    return auth()->check();
});
Broadcast::channel('game-over-channel-' . $this->gameId . '-' . $this->userId, function ($user) {
      return auth()->check();
});
