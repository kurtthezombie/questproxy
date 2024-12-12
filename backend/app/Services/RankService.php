<?php 

namespace App\Services;

use App\Models\Rank;
use App\Models\Report;
use Exception;

class RankService {

    protected $rank;

    public function __construct(Rank $rank){
        $this->rank = $rank;
    }

    public function index(){
        $rank_records = $this->rank->with(['pilot.user'])->get();

        // Prepare the response data
        $rankings = $rank_records
            ->sortByDesc('points')
            ->map(function ($rank) {
                return [
                    'pilot_username' => $rank->pilot->user->username,
                    'points' => $rank->points,
                ];
        })->values();

        return $rankings;
    }

    public function create() {
        $created =  $this->rank->create([
            'pilot_rank' => null,
            'points' => 0,
        ]);

        if (!$created) {
            throw new Exception("Failed to create rank record.");
        }

        return $created;
    }

    public function findById($id){
        return $this->rank->with('pilot.user')->findOrFail($id);
    }

    public function delete($id){
        $rank = $this->rank->findOrFail($id);

        if (!$rank->delete()){
            throw new Exception("Failed to delete rank.");
        }

        return true;
    }
}