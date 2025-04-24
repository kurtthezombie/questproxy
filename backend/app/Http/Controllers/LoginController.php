<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    use ApiResponseTrait;

    public function login(Request $request)
    {
        try {
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
            $token = $user->createToken($user->name.'Auth-Token',['*'])->plainTextToken;
    
            $pilot = $user->pilot;
            $gamer = $user->gamer;

            if ($pilot) {
                return response()->json([
                    'message' => 'Login successful.',
                    'token_type' => 'Bearer',
                    'token' => $token,
                    'authenticated_user' => $user,
                    'pilot_id' => $pilot ? $pilot->id : null,
                    'status' => true,
                ],200);
            } else if($gamer) {
                return response()->json([
                    'message' => 'Login successful.',
                    'token_type' => 'Bearer',
                    'token' => $token,
                    'authenticated_user' => $user,
                    'gamer_id' => $gamer->id,  // Return gamer id for gamer users
                    'status' => true,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Login successful, but no associated pilot or gamer role.',
                    'token_type' => 'Bearer',
                    'token' => $token,
                    'authenticated_user' => $user,
                    'status' => true,
                ], 200);
            }
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(), 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $logout = $request->user()->currentAccessToken()->delete();
            $request->user()->tokens()->delete();
            
            return $this->successResponse('Logout successful.', 200);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(), 500);
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
