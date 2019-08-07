<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Cache;


class User extends Authenticatable
{
    use  Notifiable,HasApiTokens;
    use \HighIdeas\UsersOnline\Traits\UsersOnlineTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // public function followers()//اللي يتبعوك
    // {
    //    return $this->belongsToMany(self::class, 'followers', 'follows_id', 'user_id')
    //               ->withTimestamps();
    // }

    public function invites()
    {
        return $this->belongsToMany(self::class, 'invites', 'invite_sender', 'invite_receiver')
            ->withTimestamps();
    }

    public function send_invite($invite_receiver_Id)
    {
        $this->invites()->attach($invite_receiver_Id);
        return $this;
    }

    public function cancel_invite($invite_receiver_Id)
    {
        $this->invites()->detach($invite_receiver_Id);
        return $this;
    }
    public function reject_invite($invite_sender_Id)
    {
        $this->invites()->detach($invite_sender_Id);
        return $this;
    }

    public function isInviting($invite_receiver_Id)
    {
        return (boolean) $this->invites()->where('invite_receiver',$invite_receiver_Id)->first(["users.id"]);
    }

 // public function isAvailable()
  //{
    // $this->play_mode=false;
  //}

}
