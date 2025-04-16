<?php

namespace App\Http\Controllers;

use App\Models\Rank;
use App\Services\RankService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Traits\RankOperations;

class RankController extends Controller
{
    use RankOperations, ApiResponseTrait;

    protected $rankService;

    public function __construct(RankService $rankService){
        $this->rankService = $rankService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $rankings = $this->rankService->index();
            $message = $rankings->isEmpty() ? "The leaderboard is currently empty." : "Leaderboard data has been successfully retrieved.";

            return $this->successResponse($message,200,['rankings' => $rankings]);
        } catch (Exception $e){
            return $this->failedResponse("Error: " . $e->getMessage(), 500);
        }
    }

    /**
     * rnk record should not exist wihtout a pilot.
     */
    public function store()
    {
        try {
            $created = $this->rankService->create();

            return $this->successResponse("Rank record created.",201);
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
            $rank_record = $this->rankService->findById($id);

            //continue here?
            return $this->successResponse('Rank record successfully retrieved.',200,[
                'rank_record' => [
                    'id' => $rank_record->id,
                    'rank' => $rank_record->pilot_rank,
                    'points' => $rank_record->points,
                    'username' => $rank_record->pilot->user->username,
                ]
            ]);
        } catch (ModelNotFoundException $e){
            return $this->failedResponse("Rank record {$id} is not found.",404);
        } catch (Exception $e) {
            return $this->failedResponse("Error: " . $e->getMessage(),500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $deleted = $this->rankService->delete($id);

            return $this->successResponse("Rank record deleted.",201);
        } catch (ModelNotFoundException $e){
            return $this->failedResponse("Rank record {$id} not found.",404);
        } catch (Exception $error) {
            return $this->failedResponse("Error: " . $e->getMessage(),500);
        }
    }
}
