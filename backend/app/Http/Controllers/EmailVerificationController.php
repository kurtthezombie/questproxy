<?php

namespace App\Http\Controllers;

use App\Http\Requests\MyEmailVerificationRequest;
use App\Mail\EmailVerification;
use App\Models\Otp;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Mail;

class EmailVerificationController extends Controller
{
    use ApiResponseTrait;
    public function checkVerified(Request $request){
        if ($request->email_verified_at != null) {
            return response()->json([
                'verified' => true,
                'message' => 'Email is verified',
            ]);
        }
    }

    public function verify (Request $request) {
        $user = $request->user();

        $user->email_verified_at = now();
        $user->save();

        return $this->successResponse('Email verified successfully.',200);
    }

    public function checkOtp(Request $request){
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
        ]);

        $otpRecord = Otp::where('email',$request->email)->first();

        if(!$otpRecord) {
            return $this->failedResponse('No OTP found for this email',404);
        }

        if ($otpRecord->token === $request->otp && !$otpRecord->is_activated) {
            $otpRecord->is_activated = true;
            $otpRecord->save();

            return $this->verify($request);
        }

        return $this->failedResponse('Invalid OTP or already used.',400);
    }

    public function resend(Request $request) {
        $request->validate([
            'email' => 'required|email',
        ]);

        $otpRecord = Otp::where('email',$request->email)->first();

        $otp = random_int(100000, 999999);

        if ($otpRecord) {
            $otpRecord->update([
                'token' => $otp,
                'is_activated' => false,
            ]);
        } else {
            Otp::create([
                'token' => $otp,
                'email' => $request->email,
                'is_activated' => false
            ]);
        }

        Mail::to($request->email)->send(new EmailVerification($otp));
    }
    /* 
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

    public function verify (MyEmailVerificationRequest $request) {
        //fulfill the email verification
        $request->fulfill();

        return redirect()->away('http://localhost:5173');
        /*return response()->json([
            'verified' => true,
            'message' => 'Email is successfully verified.'
        ]);*/
    //}
    /*
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
    */
}
