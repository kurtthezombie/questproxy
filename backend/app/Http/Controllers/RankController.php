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
    public function store()
    {
        try {
            $created = $this->createRankRecord();

            return response()->json([
                'status' => true,
                'message' => "Rank record created",
            ],201);
        } catch (Exception $error) {
            return response()->json([
                'status' => false,
                'message' => $error->getMessage(),
            ],500);
        }


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
                'status' => false,
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
    public function destroy(int $id)
    {
        try {
            $deleted = $this->destroyRankRecord($id);
            
            if ($deleted) {
                return response()->json([
                    'status' => true,
                    'message' => "Rank record deleted.",
                ],201);
            } else {
                return response()->json([
                    'deleted' => $deleted,
                    'status' => false,
                    'message' => "Error occured, Deletion failed.",
                ],400);
            }
        } catch (Exception $error) {
            return response()->json([
                'status' => false,
                'message' => $error->getMessage(),
            ]);
        }
    }
}
