<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\ProgressLog;
use Illuminate\Notifications\Notification;

class ProgressItemAddedNotification extends Notification
{
    protected $booking;
    protected $progressLog;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking, ProgressLog $progressLog)
    {
        $this->booking = $booking;
        $this->progressLog = $progressLog;
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
            'type' => 'progress_item_added',
            'message' => "New progress update for booking #{$this->booking->id}: {$this->progressLog->description}",
            'booking_id' => $this->booking->id,
        ];
    }
}
