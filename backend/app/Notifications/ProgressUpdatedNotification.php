<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Service;
use Illuminate\Notifications\Notification;

class ProgressUpdatedNotification extends Notification
{
    protected $service;
    protected $percentage;
    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Service $service, int $percentage, Booking $booking)
    {
        $this->service = $service;
        $this->percentage = $percentage;
        $this->booking = $booking;
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
            'type' => 'progress_updated',
            'message' => "Your booking #{$this->booking->id} is now {$this->percentage}% complete.",
            'booking_id' => $this->booking->id,
        ];
    }
}
