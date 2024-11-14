<?php

namespace App\Http\Controllers;

use App\Events\RegisteredUser;
use App\Mail\AccountDeletionMail;
use App\Models\Gamer;
use App\Models\Pilot;
use App\Models\User;
use App\Traits\RankOperations;
use App\Traits\ApiResponseTrait;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Mail;


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
        //$captcha_validated = $this->validateCaptcha($request->captcha, $request->key);
        $captcha_validated = true;
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

        try {
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
            //
            event(new RegisteredUser($user));
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(),500);
        }
    
        return $this->successResponse(
            'User created successfully',
            201,
            [
                'user' => $user,
            ],
        );
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

        //determine if pilot
        if ($user->role == 'game_pilot' || $user->role == 'game pilot')
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

    public function requestAccountDeletion(Request $request )
    {
        try {
            $user = $request->user();
            
            Mail::to($user->email)->send(new AccountDeletionMail($user));

            return $this->successResponse("Deletion email sent. Please check your inbox.",200);
        } catch (Exception $error) {
            return $this->failedResponse($error,500);
        }
    }

    //for testing, not a major function
    public function checklogin()
    {
        return $this->successResponse("I'm logged in.", 200);
    }
}
