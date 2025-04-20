<?php

namespace App\Services;

use App\Models\Booking;
use Exception;

class BookingService
{
    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function booksByService($id){
        $bookings = Booking::where('service_id', $id)->get();

        if($bookings->isEmpty())
        {
            return collect();
        }

        return $bookings;
    }

    public function create($data)
    {
        //create booking
        $booking = new Booking();
        $booking->client_id = $data['client_id'];
        $booking->service_id = $data['service_id'];

        if (!$booking->save()){
            throw new Exception('Failed to create booking.');
        }

        return $booking;
    }

    public function updateStatus($status, $booking_id){
        $booking = $this->booking->findOrFail($booking_id);
        $booking->status = $status;
        $booking->save();

        return $booking;
    }

    public function getBookingServiceDetails($booking_id)
    {
        $booking = Booking::with([
            'service.category',
            'service.pilot.user',
            'client'
        ])->findOrFail($booking_id);

        $details = [
            'description' => $booking->service->description,
            'category_title' => $booking->service->category->title,
            'pilot_username' => $booking->service->pilot->user->username,
            'client_username' => $booking->client->username,
            'price' => $booking->service->price,
            'duration' => $booking->service->duration,
        ];

        return $details;
    }
}
