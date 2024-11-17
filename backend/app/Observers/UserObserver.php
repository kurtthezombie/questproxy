<?php

namespace App\Observers;

use App\Models\Gamer;
use App\Models\Pilot;
use App\Models\Rank;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        if($user->role == 'gamer'){
            Gamer::create([
                'user_id' => $user->id
            ]);
        }
        elseif(in_array($user->role, ['game_pilot','game pilot'])){
            $rank = Rank::create();
            Pilot::create([
                'user_id' => $user->id,
                'rank_id' => $rank->id
            ]);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
