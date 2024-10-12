<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponseTrait;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    use ApiResponseTrait;

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
            return $this->failedResponse('Incorrect username or password.', 400);
        }
        //create token

        $token = $user->createToken($user->name.'Auth-Token',['*'], now()->addMinutes(30))->plainTextToken;
        //set auth info
        Auth::login($user);

        return response()->json([
            'message' => 'Login successful.',
            'token_type' => 'Bearer',
            'token' => $token,
            'authenticated_user' => Auth::user(),
            'status' => true,
        ],200);
    }

    public function logout(Request $request)
    {
        try {
            $logout = $request->user()->currentAccessToken()->delete();
            $request->user()->tokens()->delete(); //use if u wanna delete all tokens hehe
            //if logout successful
            if ($logout) {
                return response()->json([
                    'message' => 'Successfully logged out.',
                    'status' => true,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Logout failed.',
                    'status' => false,
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false,
            ],500);
        }
    }

    //to test if auth works, set bearer token in postman
    public function testAuth()
    {
        $user = Auth::user()->id;
        return response()->json([
            'user' => $user,
            'message' => "Auth user works!",
            'status' => true,
        ],200);
    }
}
