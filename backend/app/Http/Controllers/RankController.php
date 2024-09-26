<?php

namespace App\Http\Controllers;

use App\Models\Rank;
use Exception;
use Illuminate\Http\Request;
use App\Traits\RankOperations;

class RankController extends Controller
{
    use RankOperations;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rank_records = Rank::with(['pilots.user'])->get();

        // Prepare the response data
        $rankings = $rank_records->map(function ($rank) {
            $pilot = $rank->pilots->first();

            return [
                'pilot_username' => $pilot->user->username,
                'points' => $rank->points,
            ];
        });
        if ($rankings) {
            return response()->json([
                'rankings' => $rankings,
                'message' => 'Leaderboard data has been successfully retrieved.',
                'status' => true,
            ],200);
        } else {
            return response()->json([
                'rankings' => $rankings,
                'message' => 'The leaderboard is currently empty.',
                'status' => true,
            ],200);
        }
    }

    /**
     * rnk record should not exist wihtout a pilot.
     */
    public function store(Request $request)
    {
        //$this->createRankRecord();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $rank_record = Rank::findOrFail($id);

            return response()->json([
                'rank_record' => $rank_record,
                'message' => 'Rank record successfully retrieved.',
                'status' => true,
            ],200);
        } catch (Exception $error) {
            return response()->json([
                'rank_record' => $rank_record,
                'message' => 'Rank record not found.',
                'status' => true,
            ],404);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //$this->destroyRankRecord($id)
    }
}
