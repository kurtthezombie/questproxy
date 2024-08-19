<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
=======
use App\Models\User;
use Hash;
>>>>>>> origin/feature/user-register
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
<<<<<<< HEAD
=======
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


        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ],201);
    }
>>>>>>> origin/feature/user-register
}
