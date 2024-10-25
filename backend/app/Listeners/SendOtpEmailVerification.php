<?php

namespace App\Listeners;

use App\Events\RegisteredUser;
use App\Mail\EmailVerification;
use App\Models\Otp;
use Mail;

class SendOtpEmailVerification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RegisteredUser $event): void
    {
        //get user
        $user = $event->user;

        $otp = random_int(100000,999999);

        Otp::create([
            'token' => $otp,
            'email' => $user->email,
            'is_activated' => false,
        ]);

        Mail::to($user->email)->send(new EmailVerification($otp));
    }
}
