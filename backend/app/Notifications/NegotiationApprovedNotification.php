<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;

class NegotiationApprovedNotification extends Notification
{
    use Queueable;

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
        // Ensure service and pilot.user relations are loaded
        $this->booking->load('service.pilot.user');

        return [
            'type' => 'negotiation_approved',
            'booking_id' => $this->booking->id,
            'message' => 'Your negotiation for service "' . $this->booking->service->description . '" with ' . $this->booking->service->pilot->user->username . ' has been approved! Proceed to payment.',
            'service_id' => $this->booking->service->id, // Include service ID for linking if needed
            'link' => '/payment/' . $this->booking->id, // Add this link for the frontend
        ];
    }
}
