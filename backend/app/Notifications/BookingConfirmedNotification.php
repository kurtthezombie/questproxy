<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification
{
    protected $user;
    protected $service;
    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, Service $service, Booking $booking)
    {
        $this->user = $user;
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
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'booking_confirmed',
            'message' => $this->user->username . ' has booked your service #' . $this->service->id . ' on ' . $this->booking->created_at->format('M d, Y') . '.',
        ];
    }
}
