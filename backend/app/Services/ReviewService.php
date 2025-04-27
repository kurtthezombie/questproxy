<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Pilot;
use App\Models\Review;
use App\Models\Service;

class ReviewService
{
    protected $category;
    protected $review;
    protected $service;
    protected $pilot;

    public function __construct(Category $category, Review $review, Service $service, Pilot $pilot)
    {
        $this->category = $category;
        $this->review = $review;
        $this->service = $service;
        $this->pilot = $pilot;
    }

    public function index($service_id) {
        return $this->review->where('service_id',$service_id)->get();
    }

    public function findById($id) {
        return $this->review->findOrFail($id);
    }

    public function create($data){
        $data['user_id'] = auth()->id();

        $review = $this->review->create($data);

        if(!$review) {
            throw new \Exception("Failed to create review.");
        }
        
        $pilot = $this->pilot->with('rank')->find($data['pilot_id']);

        if($pilot && $pilot->rank) {
            $rank = $pilot->rank;
            $rank->points += $this->getPointsFromRating($data['rating']);
            $rank->save();
        }

        return $review;
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
            'pilot_id' => $service->pilot->id,
        ];
    }

    public function getReviewsByPilotId($pilotId) {
      
        return $this->review->where('pilot_id', $pilotId)->with('user')->get();
    }

    private function getPointsFromRating(int $rating) {
        return match ($rating) {
            1 => 0,
            2 => 1,
            3 => 3,
            4 => 6,
            5 => 10,
            default => 0,
        };
    }
}

