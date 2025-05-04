<?php

namespace App\Http\Controllers;

use App\Models\Pilot;
use App\Notifications\PilotMatchedNotification;
use App\Notifications\UserMatchedNotification;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class MatchingController extends Controller
{
    use ApiResponseTrait;

    public function matchPilot(Request $request)
    {
        try {
            $request->validate([
                'game' => 'required|integer',
                'service' => ['required', 'string', Rule::in(['level', 'grind', 'farm', 'quest'])],
                'points' => 'required|int',
                'duration' => 'nullable|string',
                'min_price' => 'nullable|numeric',
                'max_price' => 'nullable|numeric|gte:min_price',
            ]);

            $matchingPilot = $this->findMatchingPilot($request);

            if (!$matchingPilot) {
                return $this->successResponse('No matching pilots found.', 200);
            }

            // Get the logged in user and pilot's user instances
            $loggedInUser = $request->user();
            $pilotUser = $matchingPilot->user;
            
            // Send notifications to both pilot and gamer
            $this->sendNotifToPilot($pilotUser, $loggedInUser);
            $this->sendNotifToGamer($loggedInUser, $matchingPilot);

            $matchingServices = $this->filterMatchingServices($matchingPilot, $request);

            if ($matchingServices->isEmpty() && $this->hasSpecificCriteria($request)) {
                $pilotDetails = $this->formatPilotDetails($matchingPilot, []);
                return $this->successResponse('Pilot found, but no services matched the specific criteria.', 200, ['pilot_details' => $pilotDetails]);
            }

            if ($matchingPilot->services->isEmpty()) {
                return $this->successResponse('No matching pilots found with the basic criteria.', 404);
            }

            $pilotDetails = $this->formatPilotDetails($matchingPilot, $matchingServices->values()->all());
            return $this->successResponse('Matching pilot found.', 200, ['pilot_details' => $pilotDetails]);

        } catch (\Exception $e) {
            return $this->failedResponse('An internal server error occurred.', 500);
        }
    }

    private function findMatchingPilot(Request $request)
    {
        return Pilot::with(['user', 'rank', 'services' => function($query) use ($request) {
            $this->applyServiceFilters($query, $request);
        }])
        ->whereHas('services', function($query) use ($request) {
            $this->applyServiceFilters($query, $request);
        })
        ->whereHas('rank', function($query) use ($request) {
            $query->where('points', '>=', $request->points);
        })
        ->where('user_id', '!=', $request->user()->id)
        ->first();
    }

    private function applyServiceFilters($query, Request $request)
    {
        $query->where('category_id', $request->game)
              ->whereRaw('LOWER(description) LIKE ?', ['%' . strtolower($request->service) . '%'])
              ->where('availability', true);

        if ($request->has('duration') && $request->duration !== null) {
            $query->where('duration', $request->duration);
        }

        if ($request->has('min_price') && $request->min_price !== null && $request->has('max_price') && $request->max_price !== null) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        } elseif ($request->has('min_price') && $request->min_price !== null) {
            $query->where('price', '>=', $request->min_price);
        } elseif ($request->has('max_price') && $request->max_price !== null) {
            $query->where('price', '<=', $request->max_price);
        }
    }

    private function filterMatchingServices($pilot, Request $request)
    {
        return $pilot->services->filter(function ($service) use ($request) {
            $matchesDuration = true;
            if ($request->has('duration') && $request->duration !== null) {
                $matchesDuration = ($service->duration == $request->duration);
            }

            $matchesPrice = true;
            if ($request->has('min_price') && $request->min_price !== null && $request->has('max_price') && $request->max_price !== null) {
                $matchesPrice = ($service->price >= $request->min_price && $service->price <= $request->max_price);
            } elseif ($request->has('min_price') && $request->min_price !== null) {
                $matchesPrice = ($service->price >= $request->min_price);
            } elseif ($request->has('max_price') && $request->max_price !== null) {
                $matchesPrice = ($service->price <= $request->max_price);
            }

            $matchesServiceType = true;
            if ($request->has('service') && $request->service !== null) {
                $matchesServiceType = (stripos($service->description, $request->service) !== false);
            }

            return $matchesDuration && $matchesPrice && $matchesServiceType;
        })->map(function ($service) use ($pilot) {
            $service->pilot_username = $pilot->user->username ?? 'Unknown User';
            return $service;
        });
    }

    private function hasSpecificCriteria(Request $request)
    {
        return ($request->has('duration') && $request->duration !== null) ||
               ($request->has('min_price') && $request->min_price !== null) ||
               ($request->has('max_price') && $request->max_price !== null) ||
               ($request->has('service') && $request->service !== null);
    }

    private function formatPilotDetails($pilot, $services)
    {
        return [
            'user' => $pilot->user ?? null,
            'skills' => $pilot->skills ?? null,
            'bio' => $pilot->bio ?? null,
            'rank' => $pilot->rank ?? null,
            'services' => $services,
        ];
    }

    private function sendNotifToPilot($pilot, $gamer)
    {
        $pilot->notify(new PilotMatchedNotification($gamer));
    }

    private function sendNotifToGamer($gamer, $pilot)
    {
        $gamer->notify(new UserMatchedNotification($pilot->user));
    }
}