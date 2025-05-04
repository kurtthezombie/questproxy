<?php

namespace App\Http\Controllers;

use App\Events\NotificationBroadcastEvent;
use App\Models\Pilot;
use App\Notifications\PilotMatchedNotification;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class MatchingController extends Controller
{
    use ApiResponseTrait;

    public function matchPilot(Request $request)
    {
        Log::info('Match Pilot Request Received', $request->all());

        try {
            $request->validate([
                'game' => 'required|integer',
                'service' => ['required', 'string', Rule::in(['level', 'grind', 'farm', 'quest'])],
                'points' => 'required|int',
                'duration' => 'nullable|string',
                'min_price' => 'nullable|numeric',
                'max_price' => 'nullable|numeric|gte:min_price',
            ]);

            Log::info('Request Validated');

            $matchingPilot = Pilot::with(['user', 'rank', 'services' => function($query) use ($request) { // Eager load 'user'
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
            }])->whereHas('services', function($query) use ($request) {
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

            })->whereHas('rank', function($query) use ($request) {
                $query->where('points', '>=', $request->points);
            })->where('user_id', '!=', $request->user()->id)
          ->first();

            Log::info('Pilot Query Result', ['pilot' => $matchingPilot]);


            if (!$matchingPilot) {
                Log::info('No matching pilots found.');
                return $this->successResponse('No matching pilots found.', 404);
            }

            // Filter the loaded services and add pilot username
            $matchingServices = $matchingPilot->services->filter(function ($service) use ($request) {
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
                } else {
                     $matchesServiceType = true;
                }

                return $matchesDuration && $matchesPrice && $matchesServiceType;
            })->map(function ($service) use ($matchingPilot) {
                $service->pilot_username = $matchingPilot->user->username ?? 'Unknown User';
                return $service;
            });


            Log::info('Matching Services After Filtering and Adding Username', ['services' => $matchingServices->toArray()]);


             if ($matchingServices->isEmpty() && ($request->has('duration') && $request->duration !== null || $request->has('min_price') && $request->min_price !== null || $request->has('max_price') && $request->max_price !== null || $request->has('service') && $request->service !== null)) {
                 Log::info('Pilot found, but no services matched the specific criteria.');
                  $pilotDetails = [
                    'user' => $matchingPilot->user ?? null,
                    'skills' => $matchingPilot->skills ?? null,
                    'bio' => $matchingPilot->bio ?? null,
                    'rank' => $matchingPilot->rank ?? null,
                    'services' => [],
                ];
                 return $this->successResponse('Pilot found, but no services matched the specific criteria.', 200, ['pilot_details' => $pilotDetails]);

             }

             if ($matchingPilot->services->isEmpty()) {
                 Log::info('Pilot found, but has no services matching basic criteria.');
                 return $this->successResponse('No matching pilots found with the basic criteria.', 404);
            }


            $pilotDetails = [
                'user' => $matchingPilot->user ?? null,
                'skills' => $matchingPilot->skills ?? null,
                'bio' => $matchingPilot->bio ?? null,
                'rank' => $matchingPilot->rank ?? null,
                'services' => $matchingServices->values()->all(),
            ];

            Log::info('Matching pilot and services found.', ['pilot_details' => $pilotDetails]);

            return $this->successResponse('Matching pilot found.', 200, ['pilot_details' => $pilotDetails]);
        } catch (\Exception $e) {
            Log::error('Error in matchPilot: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return $this->errorResponse('An internal server error occurred.', 500);
        }
    }
}