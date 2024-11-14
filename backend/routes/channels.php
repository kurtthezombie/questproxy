<?php

use Illuminate\Support\Facades\Broadcast;

//this will be sent with notification from notification.{id}
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    \Log::info("Authorizing USER {$user->id} for CHANNEL {$id}");
    return (int) $user->id === (int) $id;
});

//useless
Broadcast::channel('notifications', function (){
    return true;
});
