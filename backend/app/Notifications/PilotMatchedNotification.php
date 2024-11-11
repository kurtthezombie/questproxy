<?php

namespace App\Notifications;

use Illuminate\Broadcasting\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PilotMatchedNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    protected $user;
    /**
     * Create a new notification instance.
     */
    public function __construct($user)
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
        return ['database','broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'A user has matched with you!',
            'user_id' => $this->user->id,
            'user_name' => $this->user->username,
        ];
    }

    public function broadcastOn(){
        return new Channel('notifications');
    }

    public function broadcastsWith(){
        return [
            'message' => 'A user has matched with you!',
            'user_id' => $this->user->id,
            'user_name' => $this->user->username,
        ];
    }

    public function broadcastAs()
    {
        // Optional: Define a custom name for the event
        return 'PilotMatchedNotification';
    }
}
