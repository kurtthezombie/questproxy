<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Notifications\Notification;

class BookingCompletedNotification extends Notification
{
    protected $pilot;
    protected $service;
    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $pilot, Service $service, Booking $booking)
    {
        $this->pilot = $pilot;
        $this->service = $service;
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
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase($notifiable)
    {
        return [
            'type' => 'booking_completed',
            'message' => $this->pilot->username . ' has marked your booking for service \'' . $this->service->description . '\' as completed on ' . $this->booking->updated_at->format('Y-m-d') . '.',
            'booking_id' => $this->booking->id,
        ];
    }
}
