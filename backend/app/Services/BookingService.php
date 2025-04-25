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

    public function updateStatus($status, $booking_id){
        $booking = $this->booking->findOrFail($booking_id);
        $booking->status = $status;
        $booking->save();

        if ($status == 'completed'){
            $booking->load([
                'service.pilot.user',
                'client', 
            ]);
            $this->sendCompletionEmail($booking);
        }

        return $booking;
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
        })->with(['service','service.pilot'])->get();

        return $bookings;
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
