<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Log;

class NotificationBroadcastEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $user;

    /**
     * Create a new event instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
        Log::info('NotificationBroadcastEvent created for user ID: ' . $this->user->id);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channel = 'App.Models.User.' . $this->user->id;
        // Log the channel the event will be broadcast on
        Log::info('Broadcasting event on channel: ' . $channel);

        return [
            new Channel($channel),
        ];
    }

    public function broadcastAs(){
        $event_name =  'notification.broadcast';
        // Log the event name being broadcasted
        Log::info('Broadcasting event as: ' . $event_name);
        return $event_name;
    }

    public function broadcastWith()
    {
        $data = [
            'message' => 'A user has matched with you!',
            'user_id' => $this->user->id,
            'user_name' => $this->user->username,
        ];

        // Log the data being sent with the broadcast
        Log::info('Broadcasting data: ', $data);

        return $data;
    }
}
