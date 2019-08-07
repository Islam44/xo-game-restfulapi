<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserChallenged extends Notification
{
    use Queueable;
    /**
     * @var User
     */
    protected $challenger;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $challenger)
    {
        //
        $this->challenger=$challenger;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'id' => $this->id,
            'read_at' => null,
            'data' => [
                'challenger_id' => $this->challenger->id,
                'challenger_name' => $this-> challenger->name,
            ],
        ];
    }
    /**
     * Get the database representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'challenger_id' => $this->challenger->id,
            'challenger_name' => $this-> challenger->name,
        ];
    }

}
