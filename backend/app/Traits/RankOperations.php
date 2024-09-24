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
}
