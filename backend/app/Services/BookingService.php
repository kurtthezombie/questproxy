<?php

namespace App\Services;

use App\Mail\ServiceCompletionMail;
use App\Models\Booking;
use Exception;
use Mail;

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

    public function markAsCompleted($booking_id){
        $booking = $this->booking->findOrFail($booking_id);

        if (now()->toDateString() < $booking->instruction->start_date) {
            throw new Exception('You cannot mark this booking as completed before the start date.');
        }

        $booking->status = 'completed';
        $booking->save();

        $booking->load([
            'service.pilot.user',
            'client', 
        ]);
        
        $this->sendCompletionEmail($booking);

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
    public function getBookingsByPilot()
    {
        $user = auth()->user();

        if (!$user || !$user->pilot) {
            throw new Exception("Pilot not found for the authenticated user.");
        }

        $pilotId = $user->pilot->id;

        $bookings = $this->booking->whereHas('service', function ($query) use ($pilotId) {
            $query->where('pilot_id', $pilotId);
        })
        ->whereHas('payment', function ($query) {  // Filter by 'paid' status in payment
            $query->where('status', 'paid');
        })
        ->with(['service', 'service.pilot', 'service.category', 'client', 'instruction', 'payment'])  // Eager load payment
        ->get();

        return $bookings;
    }

    public function getMyBookings()
    {
        $user = auth()->user();

        if (!$user) {
            throw new Exception("Authenticated user not found.");
        }

        $bookings = $this->booking->where('client_id', $user->id)
            ->whereHas('payment', function ($query) {
                $query->where('status','paid');
            })
            ->with(['service','service.pilot'])
            ->get();

        return $bookings->map(function ($booking) {
            return [
                'booking' => $booking,
                'created_at' => $booking->created_at,
                'serviceTitle' => $booking->service->description,
                'gameTitle' => $booking->service->category->title,
                'pilotName' => $booking->service->pilot->user->username,
            ];
        });
    }

    public function updateProgress($booking_id, $progress)
    {
        $booking = Booking::findOrFail($booking_id);
        $booking->progress = $progress;
        $booking->save();

        return $booking;
    }

    public function deleteBooking($booking_id)
    {
        $booking = $this->booking->findOrFail($booking_id);
        
        $booking->instruction()->delete();
    
        $booking->delete();
    }

    private function sendCompletionEmail($booking)
    {
        $service = $booking->service;
        $client = $booking->client;
        $pilot = $service->pilot->user;

        $details = (object) [
            'client_name' => $client->name,
            'description' => $service->description,
            'pilot_name' => $pilot->username,
            'id' => $service->id,
        ];

        Mail::to($client->email)->send(new ServiceCompletionMail($details));
    }
}
