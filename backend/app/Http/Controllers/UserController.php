<?php

namespace App\Http\Controllers;

use App\Models\Gamer;
use App\Models\Pilot;
use App\Models\User;
use App\Traits\RankOperations;
use App\Traits\ApiResponseTrait;
use Auth;
use DB;
use Exception;
use Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    use RankOperations, ApiResponseTrait;

    public function show($id)
    {
        $user = User::find($id);

        return (!$user)
        ?  $this->failedResponse("User {$id} not found.",404)
        :  $this->successResponse("User {$id} is found.",200,['user' => $user]);
    }

    public function index()
    {
        $users = User::all();
        
        return $this->successResponse('User records retrieved successfully.', 200, ['users' => $users]); 
    }

    public function create(Request $request)
    {
        //uncomment and just set validated to true if testing postman
        $captcha_validated = $this->validateCaptcha($request->captcha, $request->key);
        //$captcha_validated = true;
        if(!$captcha_validated){
            return $this->failedResponse('Captcha incorrect or invalid.', 400);
        }
        
        //validate inputs
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
            'f_name' => 'required|string',
            'l_name' => 'required|string',
            'contact_number' => 'required|string|max:15',
            'role' => 'required|string',
        ]);
        //create user object
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'contact_number' => $request->contact_number,
            'role' => $request->role,
        ]);

        //should admin be made through registration ? sounds dumb
        $addGamerOrPilot = ($request->role == 'gamer') ? $this->createGamer($user->id) : $this->createPilot($user->id);

        if ($addGamerOrPilot)
        {
            return $this->successResponse(
                'User created successfully',
                201,
                [
                    'user' => $user,
                    'role_created' => $addGamerOrPilot
                ],
            );
        }
        else
        {
            return $this->failedResponse("Error occurred while trying to create gamer/pilot record",500);
        }
    }

    private function createGamer(int $id)
    {
        Gamer::create(['user_id' => $id]);

        return true;
    }

    private function createPilot(int $id)
    {
        //create ranking
        $rank = $this->createRankRecord();
        $rank_id = $rank->id;
        //set id
        Pilot::create([
            'user_id' => $id, //derived from parameter
            'rank_id' => $rank_id, //derived from db query above this
        ]);

        return true;
    }

    public function edit(int $id){
        $user = User::find($id);

        if (!$user) {
            return $this->failedResponse("User account {$id} not found.", 404);
        }

        return $this->successResponse("User account {$id} found.", 200, ['user' => $user]);
    }

    public function update(Request $request, $id) {
        $request->validate([
            'email' => 'required|email',
            'f_name' => 'required|string',
            'l_name' => 'required|string',
            'contact_number' => 'required|string|max:15',
        ]);

        $user = User::find($id);
        //if user does not exist
        if(!$user) {
            return $this->failedResponse('User not found.', 404);    
        }
        
        try {
            $user->update([
                'email' => $request->email,
                'f_name' => $request->f_name,
                'l_name' => $request->l_name,
                'contact_number' => $request->contact_number, 
            ]);

            return $this->successResponse("User account has been updated successfully.",200);

        } catch (Exception $error) {
            return $this->failedResponse("Error {$error->getMessage()}", 500);
        }
    }

    public function destroy(int $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
                'status' => false,
            ],404);
        }
        //declare to have in scope
        $rank_id = null;
        $deleted_rank = null;
        
        //determine if pilot or gamer
        if ($user->role == 'game_pilot')
        {
            $pilot = Pilot::where('user_id', $id)->first();

            if ($pilot) {
                $rank_id = $pilot->rank_id;

                $pilot->delete();
            }
        }

        //delete user and cascading records
        $user->delete();

        if ($rank_id) {
            //delete ranking
            $deleted_rank = $this->destroyRankRecord($rank_id);
        }
        
        //return responses
        if (!$deleted_rank) {
            return $this->failedResponse('An error occurred during user deletion.',500);
        }
        //if deletion is successful
        return $this->successResponse('User deleted successfully.',200);
        
    }

    private function validateCaptcha(string $captcha, string $key){
        //
        $rules = ['captcha' => 'required|captcha_api:'.$key.',math'];
        $validator = validator()->make(['captcha' => $captcha], $rules);
        return !($validator->fails());
    }

    //for testing, not a major function
    public function checklogin()
    {
        return $this->successResponse("I'm logged in.", 200);
    }
}
