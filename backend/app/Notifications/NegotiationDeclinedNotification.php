<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable; // Optional: if you use queues for notifications

class NegotiationDeclinedNotification extends Notification
{
    use Queueable; // Optional

    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
         // Load relations if needed to access pilot username or service details
        $this->booking->load('service.pilot.user');

        return [
            'type' => 'negotiation_declined',
            'booking_id' => $this->booking->id,
            'message' => 'Your negotiation for service "' . $this->booking->service->description . '" with ' . $this->booking->service->pilot->user->username . ' has been declined.',
             'service_id' => $this->booking->service->id, // Include service ID for linking if needed
        ];
    }
}