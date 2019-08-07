<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
//////////
Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('signup', 'AuthController@signup');
    Route::post('login', 'AuthController@login');
});


    Route::group([
        'middleware' => 'auth:api',
        'prefix' => 'me'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
        Route::get('users', 'UserController@index');
        Route::get('invite/{user}', 'UserController@invite');
        Route::get('cancel/{user}', 'UserController@cancel_invite');
        Route::get('notifications', 'UserController@notifications');
        Route::get('game','UserController@new_game');
        Route::post('play/{gameId}', 'GameController@play');
        Route::post('game-over/{gameId}-{$winner_id}-{$loser_id}', 'GameController@gameOver');
    });





