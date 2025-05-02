<?php

namespace App\Http\Controllers;

use App\Services\ProgressService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    use ApiResponseTrait;
    protected $progressService;

    public function __construct(ProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'image' => 'required|image|mimes:jpg,png,jpeg',
            'description' => 'required|string',
            'booking_id' => 'required|exists:bookings,id'
        ]);

        try {
            $created = $this->progressService->create($data);

            return $this->successResponse('Progress update added.', 200, ['data' => $created]);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(), 500);
        }
    }

    public function getProgressByBooking($booking_id)
    {
        try {
            $records = $this->progressService->getProgressByBooking($booking_id);

            if($records->isEmpty()) {
                return $this->successResponse('No progress updates yet.', 200, ['data' => []]);
            }
            
            return $this->successResponse('Progress retrieved successfully.', 200, ['data' => $records]);
        } catch (Exception $e){
            return $this->failedResponse($e->getMessage(), 500);
        }
    }

    public function delete($id)
    {
        try {
            $this->progressService->delete($id);
            
            return $this->successResponse("Successfully deleted progress item {$id}.", 200);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(), 500);
        }
    }
}

