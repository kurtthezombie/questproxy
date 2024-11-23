<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Instruction;
use App\Services\BookingService;
use App\Services\InstructionService;
use App\Traits\ApiResponseTrait;
use Crypt;
use Exception;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    use ApiResponseTrait;

    protected $bookingService;
    protected $instructionService;

    public function __construct(BookingService $bookingService, InstructionService $instructionService){
        $this->bookingService = $bookingService;
        $this->instructionService = $instructionService;
    }

    public function show($booking_id)
    {
        $booking = Booking::with('service', 'instruction')->findOrFail($booking_id);

        return $this->successResponse('Booking retrieved.', 200, ['booking' => $booking]);
    }

    public function store(Request $request)
    {
        //validate
        $data = $request->validate([
            'client_id' => 'required',
            'service_id' => 'required',
            'additional_notes' => 'required|string',
            'credentials_username' => 'required|string',
            'credentials_password' => 'required|string',
        ]);

        //create booking
        $booking = $this->bookingService->create($data);

        if (!$booking) {
            return $this->failedResponse('Booking failed.', 500);
        }
        $instruction = $this->instructionService->create($booking->id,$data);

        if (!$instruction) {
            return $this->failedResponse('Instruction creation failed', 500);
        }

        //return success response
        return $this->successResponse('Booking and instruction created successfully.', 200);
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

        try {
            $updated = $this->bookingService->updateStatus($request->status,$booking_id);
            return $this->successResponse('Booking status updated successfully', 200);
        } catch (Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage(), 500);
        }
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
