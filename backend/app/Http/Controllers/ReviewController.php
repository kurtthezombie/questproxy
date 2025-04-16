<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Services\ReviewService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use ApiResponseTrait;

    protected $reviewService;  

    public function __construct(ReviewService $reviewService) {
        $this->ReviewService = $reviewService;
    }

    public function index($service_id)
    {
        try {
            $reviews = $this->reviewService->index();
            $message = $reviews->isEmpty() ? "No reviews yet." : "Reviews for service {$service_id} retrieved.";
            
            return $this->successResponse($message, 200, ['reviews' => $reviews]);
        } catch (Exception $e) {
            return $this->failedResponse("Error: " . $e->getMessage(), 500);
        }
    }

    //assuming the service_id is included in the form as hidden input
    public function store(Request $request)
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
            'service_id' => 'required|exists:services,id',
            'pilot_id' => 'required|exists:pilots,id',
        ]);

        try {
            $review = $this->reviewService->create($data);
            return $this->successResponse('Review created.',201,['review' => $review]);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(),500);
        }
    }

    public function show($review_id){
        try {
            $review = $this->reviewService->findById($review_id);
            return $this->successResponse('Review retrieved.',200,['review'=>$review]);
        } catch (ModelNotFoundException $e) {
            return $this->failedResponse("Review {$review_id} is not found", 404);
        }catch (Exception $e) {
            return $this->failedResponse("Error: " . $e->getMessage(),500);
        }
    }

    //in list of modules this was for admin only.
    public function destroy($review_id)
    {
        try {
            $deleted = $this->reviewService->destroy($review_id);

            return $this->successResponse('Review deleted.',200);
        } catch (ModelNotFoundException $e) {
            return $this->failedResponse("Review {$review_id} is not found", 404);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(),500);
        }
    }
}
