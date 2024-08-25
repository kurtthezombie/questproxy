<?php

namespace App\Http\Controllers;

use App\Models\Gamer;
use App\Models\Pilot;
use App\Models\User;
use DB;
use Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
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
        $addGamerOrPilot = ($request->role == 'g') ? $this->createGamer($request->id) : $this->createPilot($request->id);

        if ($addGamerOrPilot)
        {
            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ],201);
        }
        else
        {
            return response()->json([
                'message' => 'Error occurred while trying to create gamer/pilot record',
            ],500);
        }
        
       
       
    }

    private function createGamer(int $id)
    {
        Gamer::create(['user_id' => $id]);
    }

    private function createPilot(int $id)
    {
        //create ranking
        $rank_id = DB::table('ranking')->insertGetId([
            'pilot_rank' => null,
            'points' => 0,
        ]);
        //set id 
        Pilot::create([
            'user_id' => $id,
            'rank_id' => $rank_id,
        ]);
    }
}
