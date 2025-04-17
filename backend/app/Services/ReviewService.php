<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Review;
use App\Models\Service;

class ReviewService
{
    protected $category;
    protected $review;
    protected $service;

    public function __construct(Category $category, Review $review, Service $service)
    {
        $this->category = $category;
        $this->review = $review;
        $this->service = $service;
    }

    public function index($service_id) {
        return $this->review->where('service_id',$service_id)->get();
    }

    public function findById($id) {
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

    public function getServiceInfo($id){
        $service = $this->service->with('pilot.user')->findOrFail($id);

        $categoryTitle = $this->category
            ->where('game', $service->game)
            ->value('title');
        
        return [
            'description' => $service->description,
            'game' => $categoryTitle,
            'pilot_name' => $service->pilot->user->username,
        ];
    }
}

