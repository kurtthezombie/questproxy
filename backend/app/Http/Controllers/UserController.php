<?php

namespace App\Http\Controllers;

use App\Models\Gamer;
use App\Models\Pilot;
use App\Models\User;
use App\Traits\RankOperations;
use Auth;
use DB;
use Exception;
use Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    use RankOperations;

    public function show($id)
    {
        $user = User::find($id);

        if ($user)
        {
            return response()->json($user,200);
        }
        else {
            return response()->json([
                'message' => 'User not found',
                'status' => false,
            ],404);
        }
    }

    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function create(Request $request)
    {
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
            return response()->json([
                'message' => 'User created successfully',
                'user' => $user,
                'role_created' => $addGamerOrPilot,
                'status' => true,
            ],201);
        }
        else
        {
            return response()->json([
                'message' => 'Error occurred while trying to create gamer/pilot record',
                'status' => false,
            ],500);
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
            return response()->json([
                'status' => false,
                'message' => "User account $id not found",
            ],404);
        }

        return response()->json([
            'user' => $user,
            'status' => true,
            'message' => "User account $id found."
        ]);
    }

    public function update(Request $request, $id) {
        $request->validate([
            'email' => 'required|string|email',
            'f_name' => 'required|string',
            'l_name' => 'required|string',
            'contact_number' => 'required|string|max:15',
        ]);

        $user = User::find($id);
        //if user does not exist
        if(!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ],404);    
        }
        
        try {
            $user->update([
                'email' => $request->email,
                'f_name' => $request->f_name,
                'l_name' => $request->l_name,
                'contact_number' => $request->contact_number, 
            ]);

            return response()->json([
                'message' => "User account has been updated successfully.",
                'status' => true,
            ], 200);

        } catch (Exception $error) {
            return response()->json([
                'status' => false,
                'message' => "Error {$error->getMessage()}",
            ],500);
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
        if ($deleted_rank){
            return response()->json([
                'message' => 'User deleted successfully.',
                'status' => true,
            ],200);
        } else {
            return response()->json([
                'message' => 'An error occured during deletion',
                'status' => true,
            ],500);
        }
    }
    //for testing, not a major function
    public function checklogin()
    {
        return response([
            'message' => 'im logged in.',
            'status' => true,
        ]);
    }
}
