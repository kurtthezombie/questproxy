<?php

namespace App\Traits;
use App\Models\Rank;
use Exception;


trait RankOperations
{
    public function createRankRecord() {
        try {
            $rank = Rank::create([
                'pilot_rank' => null,
                'points' => 0,
            ]);
            return $rank;
        } catch (Exception $error) {
            \Log::error('Failed to create record: '. $error->getMessage());
            return null;
        }
    }

    public function destroyRankRecord(int $id) {
        try {
            $rank = Rank::destroy($id);

            return true;
        } catch (Exception $error) {
            \Log::error('Failed to delete record: '. $error->getMessage());
            return null;
        }
    }

    public function addPoints(int $rank_id, int $points) {
        $rank = Rank::findOrFail($rank_id);
        //if no rank record found
        if (!$rank) {
            throw new Exception('Rank not found.');
        }
        //add points
        $rank->points += $points;
        $rank->save();
        //return rank
        return $rank;
    }
    public function minusPoints(int $rank_id, int $points) {
        $rank = Rank::findOrFail($rank_id);
        //if no rank record found
        if (!$rank) {
            throw new Exception('Rank not found.');
        }
        //minus points
        $rank->points = max(0, $rank->points - $points);
        $rank->save();
        //return rank
        return $rank;
    }
}
