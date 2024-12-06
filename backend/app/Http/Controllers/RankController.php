<?php

namespace App\Http\Controllers;

use App\Models\Rank;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use App\Traits\RankOperations;

class RankController extends Controller
{
    use RankOperations, ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rank_records = Rank::with(['pilot.user'])->get();

        // Prepare the response data
        $rankings = $rank_records->map(function ($rank) {
            $pilot = $rank->pilot;

            return [
                'pilot_username' => $pilot->user->username,
                'points' => $rank->points,
            ];
        });
        if ($rankings) {
            return $this->successResponse('Leaderboard data has been successfully retrieved.',200,['rankings' => $rankings]);
        } else {
            return $this->successResponse('The leaderboard is currently empty.',200,['rankings'=>$rankings]);
        }
    }

    /**
     * rnk record should not exist wihtout a pilot.
     */
    public function store()
    {
        try {
            $created = $this->createRankRecord();

            return $this->successResponse("Rank record created",201);
        } catch (Exception $error) {
            return $this->failedResponse($error->getMessage(),500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $rank_record = Rank::with('pilot.user')->findOrFail($id);
            $username = $rank_record->pilot->user->username;
            //continue here?
            return $this->successResponse('Rank record successfully retrieved.',200,[
                'rank_record' => [
                    'id' => $rank_record->id,
                    'rank' => $rank_record->pilot_rank,
                    'points' => $rank_record->points,
                    'username' => $username,
                ]
            ]);

        } catch (Exception $error) {
            return $this->failedResponse('Rank record not found.',404);
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
            if (!$deleted) {
                return $this->failedResponse("Error occured, Deletion failed.", 400);
            }

            return $this->successResponse("Rank record deleted.",201);
        } catch (Exception $error) {
            return $this->failedResponse($error->getMessage(),500);
        }
    }
}
