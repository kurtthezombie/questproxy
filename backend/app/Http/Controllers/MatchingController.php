<?php

namespace App\Http\Controllers;

use App\Events\NotificationBroadcastEvent;
use App\Models\Pilot;
use App\Notifications\PilotMatchedNotification;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Str;

class MatchingController extends Controller
{
    use ApiResponseTrait;
    public function matchPilot(Request $request){
        //game and task details from the form input
        //validate
        $request->validate([
            'game' => 'required|int',
            'service' => 'required|string',
            'points' => 'required|int' 
        ]);
        //query matching pilots
        $matchingPilot = Pilot::with('rank') // Eager load the 'rank' relationship
            ->whereHas('services', function($query) use ($request) {
                $query->where('category_id', $request->game)
                    ->where('description', 'like', '%' . $request->service . '%')
                    ->where('availability', true);
            })->whereHas('rank', function($query) use ($request) {
                $query->where('points', '>=', $request->points);
            })->where('user_id', '!=', $request->user()->id)
            ->get();

        //check results
        if ($matchingPilot->isEmpty()){
            return $this->successResponse('No matching pilots found.', 200);
        }

        $randomPilot = $matchingPilot->random();

        $pilotUser = $randomPilot->user;
        $user = $request->user();

        $this->sendNotifToPilot($pilotUser, $user);

        //event(new NotificationBroadcastEvent($user));
        //return response
        return $this->successResponse('Matching pilots found.', 200, ['pilot' => $pilotUser, 'pilot_details' => $randomPilot]);
    }

    private function sendNotifToPilot($pilot, $gamer)
    {
        $pilot->notify(new PilotMatchedNotification($gamer));
        //event(new NotificationBroadcastEvent($gamer));
    }
}
