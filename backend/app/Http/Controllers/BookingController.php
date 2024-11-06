<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Instruction;
use App\Traits\ApiResponseTrait;
use Crypt;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    use ApiResponseTrait;

    public function show($booking_id)
    {
        $booking = Booking::with('service', 'instruction')->findOrFail($booking_id);

        return $this->successResponse('Booking retrieved.', 200, ['booking' => $booking]);
    }

    public function store(Request $request)
    {
        //validate
        $request->validate([
            'client_id' => 'required',
            'service_id' => 'required',
            'additional_notes' => 'required|string',
            'credentials_username' => 'required|string',
            'credentials_password' => 'required|string',
        ]);

        //create booking
        $booking = Booking::create([
            'client_id' => $request->client_id,
            'service_id' => $request->service_id
        ]);

        if (!$booking) {
            return $this->failedResponse('Booking failed.', 500);
        }

        //create booking instruction
        $instruction = Instruction::create([
            'booking_id' => $booking->id,
            'additional_notes' => $request->additional_notes,
            'credentials_username' => Crypt::encryptString($request->credentials_username),
            'credentials_password' => Crypt::encryptString($request->credentials_password),
        ]);

        if (!$instruction) {
            return $this->failedResponse('Instruction creation failed', 500);
        }
        //return success response
        return $this->successResponse('Booking created successfully.', 200);
    }

    public function destroy($booking_id)
    {
        $destroyed = Booking::destroy($booking_id);

        if (!$destroyed) {
            return $this->failedResponse('Failed to delete booking', 500);
        }
        return $this->successResponse('Booking deleted.', 204);
    }

    public function updateStatus(Request $request, $booking_id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $booking = Booking::findOrFail($booking_id);
        $booking->status = $request->status;
        $booking->save();

        return $this->successResponse('Booking status updated successfully', 200);
    }

    public function updateInstruction(Request $request, $booking_id)
    {
        $request->validate([
            'additional_notes' => 'nullable|string',
            'credentials_username' => 'nullable|string',
            'credentials_password' => 'nullable|string',
        ]);

        $instruction = Instruction::where('booking_id', $booking_id)->first();
        if (!$instruction) {
            return $this->failedResponse('Booking instruction not found.', 404);
        }
        // Update instruction fields if provided
        if ($request->has('additional_notes')) {
            $instruction->additional_notes = $request->additional_notes;
        }
        if ($request->has('credentials_username')) {
            $instruction->credentials_username = Crypt::encryptString($request->credentials_username);
        }
        if ($request->has('credentials_password')) {
            $instruction->credentials_password = Crypt::encryptString($request->credentials_password);
        }

        // Save the updated instruction
        $instruction->save();

        return $this->successResponse('Booking instruction updated successfully.', 200);
    }

    //booking by services
    public function booksByService($service_id)
    {
        //fetch books with certain service_id
        $bookings = Booking::where('service_id', $service_id)->get();

        if ($bookings->isEmpty()) {
            return $this->failedResponse('No bookings yet for this service.', 204);
        }

        return $this->successResponse(
            "Bookings of Service $service_id retrieved.",
            200,
            ['bookings' => $bookings]
        );
    }

    //booking by user
    public function booksByClient($client_id)
    {
        $bookings = Booking::where('client_id', $client_id);

        if ($bookings->isEmpty()) {
            return $this->successResponse("Client has not booked yet.", 204);
        }

        return $this->successResponse("Bookings by Client $client_id retrieved", 200);
    }

    public function getBookingInstructions($booking_id) {
        $booking = Booking::find($booking_id);

        if (!$booking) {
            return $this->failedResponse('Booking not found',404);
        }
        $instruction = $booking->instruction;

        if (!$instruction){
            return $this->failedResponse('No instructions found for this booking', 404);
        }

        // Decrypt and set values directly on the instruction object
        $instruction->credential_username = Crypt::decryptString($instruction->credential_username);
        $instruction->credential_password = Crypt::decryptString($instruction->credential_password);

        return $this->successResponse(
            "Instructions for booking $booking_id is retrieved.",
            200,
            ['instruction' => $instruction],
        );
    }
}
