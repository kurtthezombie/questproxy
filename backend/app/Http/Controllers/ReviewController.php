<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use ApiResponseTrait;

    public function index($service_id)
    {
        $reviews = Review::where('service_id', $service_id)->get();

        return $this->successResponse("Reviews for service {$service_id} retrieved.", 200, ['reviews' => $reviews]);
    }

    //assuming the service_id is included in the form as hidden input
    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
            'service_id' => 'required|exists:services,id',
            'pilot_id' => 'required|exists:pilots,id',
        ]);

        try {
            $review = Review::create([
                'rating' => $request->rating,
                'comment' => $request->comment,
                'service_id' => $request->service_id,
                'pilot_id' => $request->pilot_id,
            ]);
            return $this->successResponse('Review created.',201,['review' => $review]);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(),500);
        }
    }

    public function show($review_id){
        try {
            $review = Review::findOrFail($review_id);
            return $this->successResponse('Review retrieved.',200,['review'=>$review]);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(),500);
        }
    }

    //in list of modules this was for admin only.
    public function destroy($review_id)
    {
        try {
            $deleted = Review::destroy($review_id);

            return $this->successResponse('Review deleted.',200);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(),500);
        }
    }
}
