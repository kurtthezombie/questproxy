<?php

namespace App\Http\Controllers;

use App\Models\Pilot;
use Illuminate\Http\Request;

class PilotController extends Controller
{
    //
    public function edit($id)
    {
        //get pilot record
        $pilot = Pilot::find($id);

        if ($pilot)
        {
            //return data and fill up the forms 
            return response()->json([
                'pilot' => $pilot,
                'message' => 'Pilot record retrieved.'
            ],200);
        } else {
            return response()->json([
                'message' => 'Failed to retrieved pilot record'
            ],500);
        }
    }
    public function update(Request $request, int $id)
    {
        //validate inputs
        $request->validate([
            'skills' => 'required|string',
            'bio' => 'required|string',
        ]);
        //retrieve pilot
        $pilot = Pilot::find($id);

        if ($pilot)
        {
            //replace them with the request
            $pilot->update([
                'skills' => $request->skills,
                'bio' => $request->bio
            ]); 

            return response()->json([
                'message' => "Pilot has been updated successfully."
            ],200);
        }
        else {
            return response()->json([
                'message' => 'Pilot record cannot be retrieved'
            ],404);
        }
    }   
}
