<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Instruction;
use App\Services\BookingService;
use App\Services\InstructionService;
use App\Traits\ApiResponseTrait;
use Crypt;
use DB;
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
        $client_id = auth()->user()->id;

        //validate
        $data = $request->validate([
            'service_id' => 'required',
            'start_date' => 'required|date',
            'communication_link' => 'required|string',
            'additional_notes' => 'nullable|string',
        ]);

        $data['client_id'] = $client_id;
        
        DB::beginTransaction();

        try {
            //create booking
            $booking = $this->bookingService->create($data);
            
            $this->instructionService->create($booking->id,$data);
            
            DB::commit();

            //return success response
            return $this->successResponse('Booking and instruction created successfully.', 200, ['booking' => $booking]);
        } catch (Exception $e) {
            DB::rollBack();
            
            return $this->failedResponse("Error: " . $e->getMessage(), 500);
        }
    }

    public function destroy($booking_id)
    {
        $destroyed = Booking::destroy($booking_id);

        if (!$destroyed) {
            return $this->failedResponse('Failed to delete booking', 500);
        }
        return $this->successResponse('Booking deleted.', 204);
    }

    public function markAsCompleted($booking_id)
    {
        try {
            $updated = $this->bookingService->markAsCompleted($booking_id);
            return $this->successResponse('Booking status updated successfully', 200);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(), 500);
        }
    }

    public function updateInstruction(Request $request, $booking_id)
    {
        $data = $request->validate([
            'additional_notes' => 'nullable|string',
            'credentials_username' => 'nullable|string',
            'credentials_password' => 'nullable|string',
        ]);
        try {
            $instruction = $this->instructionService->update($booking_id, $data);

            return $this->successResponse('Instructions updated successfully.',200);
        } catch (Exception $e){
            return $this->failedResponse('Error: ' . $e->getMessage(), 500);
        }   
    }

    //booking by services
    public function booksByService($service_id)
    {
        try {   
            $bookings = $this->bookingService->booksByService($service_id);

            $message = $bookings->isEmpty()
            ? "No bookings found for Service $service_id."
            : "Bookings of Service $service_id retrieved.";

            return $this->successResponse(
                $message,
                200,
                ['bookings' => $bookings->isEmpty() ? [] : $bookings]
            );
        } catch (Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage(),500);
        }
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
        try {
            $instruction = $this->instructionService->retrieveInstructions($booking_id);

            return $this->successResponse(
                "Instructions for booking $booking_id is retrieved.",
                200,
                ['instruction' => $instruction],
            );
        } catch(Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage(), 500);
        }
    }

    public function getServiceDetails($booking_id)
    {
        try {
            $details = $this->bookingService->getBookingServiceDetails($booking_id);

            $this->successResponse('Service Booking Details retrieved',200,['data' => $details]);
        } catch (Exception $e) {
            $this->failedResponse($e->getMessage(), 500);
        }
    }
    
    public function getBookingByPilot()
    {
        try {
            $bookings = $this->bookingService->getBookingsByPilot();
            
            return $this->successResponse('Bookings retrieved.', 200,['data' => $bookings]);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(), 500);
        }
    }

    public function booksByMe()
    {
        try {
            $bookings = $this->bookingService->getMyBookings();

            if ($bookings->isEmpty()) {
                return $this->successResponse('User has not booked yet.', 200);
            }

            return $this->successResponse('Bookings retrieved.', 200, ['bookings' => $bookings]);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(), 500);
        }
    }

    public function updateProgress(Request $request, $booking_id)
    {
        $data = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
        ]);

        try {
            $updatedBooking = $this->bookingService->updateProgress($booking_id, $data['progress']);

            return $this->successResponse('Booking progress updated successfully.', 200, ['data' => $updatedBooking]);
        } catch (Exception $e) {
            return $this->failedResponse('Error: ' . $e->getMessage(), 500);
        }
    }
}
