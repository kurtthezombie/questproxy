<?php

namespace App\Notifications;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Log;
use App\Models\User;

class PilotMatchedNotification extends Notification 
{
    protected $user;
    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => 'A user has matched with you!',
            'user_id' => $this->user->id,
            'user_name' => $this->user->username,
        ];
    }


    // public function toBroadcast(object $notifiable) : BroadcastMessage {
    //     Log::info('Broadcasting NOTIFICATION for USER', ['user' => $this->user]);
    //     return (new BroadcastMessage([
    //             'message' => 'A user has matched with you!',
    //             'user_id' => $this->user->id,
    //             'user_name' => $this->user->username,
    //         ]))->onQueue('broadcasts');
    // }

    // public function broadcastOn()
    // {
    //     // Broadcasting to a private channel based on the user ID
    //     return new Channel('App.Models.User.' . $this->user->id);
    // }

    // public function broadcastAs()
    // {
    //     // Optional: Define a custom name for the event
    //     return 'PilotMatchedNotification';
    // }
}
