<?php

namespace App\Http\Controllers;

use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    //
    public function login(Request $request)
    {
        //validate inputs
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        //check if credentials match
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password,$user->password))
        {
            return response()->json([
                'message' => 'Incorrect username or password'
            ],401);
        }
        //create token

        $token = $user->createToken($user->name.'Auth-Token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token_type' => 'Bearer',
            'token' => $token
        ],200);
    }

    public function logout(Request $request)
    {
        $logout = $request->user()->currentAccessToken()->delete();

        //if logout successful
        if ($logout)
        {
            return response()->json([
                'message' => 'Successfully logged out.'
            ],200);
        }
        else 
        {
            return response()->json([
                'message' => 'Logout failed. Please try again.'
            ],500);
        }
    }
}
