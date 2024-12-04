<?php

namespace App\Services;

use App\Models\Review;

class ReviewService
{
    protected $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    public function index($service_id) {
        return $this->review->where('service_id',$service_id)->get();
    }

    public function show($id) {
        return $this->review->findOrFail($id);
    }

    public function create($data){
        $created = $this->review->create($data);

        if(!$created) {
            throw new \Exception("Failed to create review.");
        }

        return $created;
    }

    public function destroy($id){
        $review = $this->review->findOrFail($id);

        if(!$review->delete()){
            throw new \Exception("Failed to delete the review.");
        }

        return true;
    }
}

