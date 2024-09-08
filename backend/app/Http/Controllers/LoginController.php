<?php

namespace App\Http\Controllers;

use App\Models\User; 
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{

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

        $token = $user->createToken($user->name.'Auth-Token',['*'], now()->addMinutes(30))->plainTextToken;
        //set auth info
        Auth::login($user);

        return response()->json([
            'message' => 'Login successful',
            'token_type' => 'Bearer',
            'token' => $token,
            'authenticated_user' => Auth::user(),
        ],200);
    }

    public function logout(Request $request)
    {
        $logout = $request->user()->currentAccessToken()->delete();
        //$request->user()->tokens()->delete(); //use if u wanna delete all tokens hehe
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
    public function testAuth()
    {
        $user = Auth::user();
        return response()->json([
            'user' => $user,
            'message' => "Auth user works!",
        ],200);
    }
}
