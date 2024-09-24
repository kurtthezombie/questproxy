<?php

namespace App\Http\Controllers;

use App\Models\Gamer;
use App\Models\Pilot;
use App\Models\User;
use App\Traits\RankOperations;
use Auth;
use DB;
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
            'status' => 'required|string',
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
            'status' => $request->status,
            'role' => $request->role,
        ]);

        //should admin be made through registration ? sounds dumb
        $addGamerOrPilot = ($request->role == 'g') ? $this->createGamer($user->id) : $this->createPilot($user->id);

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
        $rank_id = $this->createRankRecord()->id;
        //set id
        Pilot::create([
            'user_id' => $id, //derived from parameter
            'rank_id' => $rank_id, //derived from db query above this
        ]);

        return true;
    }

    public function destroy(int $id)
    {
        $user = User::find($id);
        //declare out of scope

        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
                'status' => false,
            ],404);
        }

        $rank_id = null;
        
        //determine if pilot or gamer
        if ($user->role == 'p')
        {
            $pilot = Pilot::where('user_id', $id)->first();
            $rank_id = $pilot->rank_id;
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

    public function checklogin()
    {
        return response([
            'message' => 'im logged in.',
            'status' => true,
        ]);
    }
}
