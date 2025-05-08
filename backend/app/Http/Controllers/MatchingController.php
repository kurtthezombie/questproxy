<?php

namespace App\Http\Controllers;

use App\Models\Pilot;
use App\Notifications\PilotMatchedNotification;
use App\Notifications\UserMatchedNotification;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Models\Service;

class MatchingController extends Controller
{
    use ApiResponseTrait;

    public function matchPilot(Request $request)
    {
        try {
            // Get user's preferences
            $userPreference = $request->user()->preference;
            
            if (!$userPreference) {
                return $this->successResponse('Please set your preferences first.', 200);
            }

            $matchingPilot = $this->findMatchingPilot($userPreference);

            if (!$matchingPilot) {
                return $this->successResponse('No matching pilots found.', 200);
            }

            // Get the logged in user and pilot's user instances
            $loggedInUser = $request->user();
            $pilotUser = $matchingPilot->user;
            
            // Send notifications to both pilot and gamer
            $this->sendNotifToPilot($pilotUser, $loggedInUser);
            $this->sendNotifToGamer($loggedInUser, $matchingPilot);

            $matchingServices = $this->filterMatchingServices($matchingPilot, $userPreference);

            if ($matchingServices->isEmpty() && $this->hasSpecificCriteria($userPreference)) {
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

    private function findMatchingPilot($preference)
    {
        return Pilot::with(['user', 'rank', 'services' => function($query) use ($preference) {
            $this->applyServiceFilters($query, $preference);
        }])
        ->whereHas('services', function($query) use ($preference) {
            $this->applyServiceFilters($query, $preference);
        })
        ->whereHas('rank', function($query) use ($preference) {
            $query->where('points', '>=', $preference->points);
        })
        ->where('user_id', '!=', $preference->user_id)
        ->inRandomOrder()
        ->first();
    }

    private function applyServiceFilters($query, $preference)
    {
        $query->where('category_id', $preference->game_id)
              ->whereRaw('LOWER(description) LIKE ?', ['%' . strtolower($preference->service) . '%'])
              ->where('availability', true);

        if ($preference->duration) {
            $query->where('duration', $preference->duration);
        }

        if ($preference->max_price) {
            $query->where('price', '<=', $preference->max_price);
        }
    }

    private function filterMatchingServices($pilot, $preference)
    {
        return $pilot->services->filter(function ($service) use ($preference) {
            $matchesDuration = true;
            if ($preference->duration) {
                $matchesDuration = ($service->duration == $preference->duration);
            }

            $matchesPrice = true;
            if ($preference->max_price) {
                $matchesPrice = ($service->price <= $preference->max_price);
            }

            $matchesServiceType = true;
            if ($preference->service) {
                $matchesServiceType = (stripos($service->description, $preference->service) !== false);
            }

            return $matchesDuration && $matchesPrice && $matchesServiceType;
        })->map(function ($service) use ($pilot) {
            $service->pilot_username = $pilot->user->username ?? 'Unknown User';
            return $service;
        });
    }

    private function hasSpecificCriteria($preference)
    {
        return $preference->duration || $preference->max_price || $preference->service;
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

    public function matchServices(Request $request)
    {
        try {
            $validated = $request->validate([
                'game' => 'required|exists:categories,id',
                'service' => 'required|string',
                'points' => 'nullable|integer|min:0',
                'duration' => 'nullable|integer|max:90',
                'min_price' => 'required|numeric|min:0',
                'max_price' => 'required|numeric|min:0',
            ]);

            $services = Service::with(['pilot.user', 'pilot.rank', 'category'])
                ->whereHas('pilot.rank', function($query) use ($validated) {
                    if ($validated['points']) {
                        $query->where('points', '>=', $validated['points']);
                    }
                })
                ->where('category_id', $validated['game'])
                ->where('availability', true)
                ->where('price', '>=', $validated['min_price'])
                ->where('price', '<=', $validated['max_price'])
                ->whereRaw('LOWER(description) LIKE ?', ['%' . strtolower($validated['service']) . '%']);

            if ($validated['duration']) {
                $services->where('duration', '<=', $validated['duration']);
            }

            $services = $services->get()->map(function ($service) {
                return [
                    'id' => $service->id,
                    'description' => $service->description,
                    'price' => $service->price,
                    'duration' => $service->duration,
                    'availability' => $service->availability,
                    'pilot_username' => $service->pilot->user->username ?? 'Unknown User',
                    'pilot_points' => $service->pilot->rank->points ?? 0,
                    'category_id' => $service->category->id ?? null,
                    'category_title' => $service->category->title ?? 'Unknown Game'
                ];
            });

            return $this->successResponse(
                $services->isEmpty() ? 'No services found matching your criteria.' : 'Services found successfully.',
                200,
                ['services' => $services]
            );

        } catch (\Exception $e) {
            return $this->failedResponse('An error occurred while searching for services: ' . $e->getMessage(), 500);
        }
    }
}