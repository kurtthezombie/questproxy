<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function notice(Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'verified' => true,
                'message' => 'Email is verified.',
            ]);
        }
        else
        {
            return response()->json([
                'verified' => false,
                'message' => 'Please verify your email address.',
            ]);
        }
    }

    public function verify (EmailVerificationRequest $request) {
        //fulfill the email verification
        $request->fulfill();

        return response()->json([
            'verified' => true,
            'message' => 'Email is successfully verified.'
        ]);
    }

    public function send (Request $request) {
        if(!$request->user()){
            return response()->json([
                'message' => 'Unauthenticated'
            ]);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'status' => true,
            'message' => 'Verification link sent!',
        ],200);
    }
}
