<?php

namespace App\Services;

use App\Events\NotificationBroadcastEvent;
use App\Models\Pilot;
use App\Notifications\PilotMatchedNotification;
use Illuminate\Support\Str;

class MatchingService
{
    protected $pilot;

    public function __construct(Pilot $pilot)
    {
        $this->pilot = $pilot;
    }

    public function findMatchingPilot(array $data, $user)
    {
        $game = Str::snake($data['game']);

        // Query matching pilots
        $matchingPilots = $this->pilot->whereHas('services', function ($query) use ($data, $game) {
            $query->where('game', $game)
                ->where('description', 'like', '%' . $data['task'] . '%')
                ->where('points', '>=', $data['points']);
        })->get();

        if ($matchingPilots->isEmpty()) {
            return ['pilot' => null];
        }

        // Notify the first matching pilot
        $pilot = $matchingPilots->first();
        $pilotUser = $pilot->user ?? null;

        if ($pilotUser) {
            $pilotUser->notify(new PilotMatchedNotification($user));
            event(new NotificationBroadcastEvent($user));
        }

        return ['pilot' => $pilotUser];
    }
}
 