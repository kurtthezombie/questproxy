<?php

namespace App\Http\Controllers;

use App\Events\NotificationBroadcastEvent;
use App\Models\Pilot;
use App\Notifications\PilotMatchedNotification;
use App\Services\MatchingService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Str;

class MatchingController extends Controller
{
    use ApiResponseTrait;

    protected $matchingService;

    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    public function matchPilot(Request $request){
        //game and task details from the form input
        //validate
        $validated = $request->validate([
            'game' => 'required|string',
            'task' => 'required|string',
            'points' => 'required|integer' 
        ]);

        //call service method to find a matching pilot
        $result = $this->matchingService->findMatchingPilot($validated, $request->user());

        if (!$result['pilot']){
            return $this->successResponse('No matching pilots found.', 404);
        }

        $pilot = $result['pilot'];

        //return response
        return $this->successResponse(
            $pilot ? 'Matching pilots found.' : 'No matching pilots found.', 
            $pilot ? 200 : 404, 
            ['pilot' => $pilot]
        );
    }
}
