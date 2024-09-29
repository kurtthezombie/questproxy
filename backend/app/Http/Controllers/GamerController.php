<?php

namespace App\Http\Controllers;

use App\Models\Gamer;
use Exception;
use Illuminate\Http\Request;

class GamerController extends Controller
{
    public function edit($id){
        $gamer = Gamer::find($id);

        if (!$gamer) {
            return response()->json([
                'status' => false,
                'message' => "Gamer account $id not found",
            ],404);
        }

        return response()->json([
            'gamer' => $gamer,
            'status' => true,
            'message' => "Gamer account $id found."
        ]);
    }

    public function update(Request $request, $id) {
        $request->validate([
            'gamer_preference' => 'required|string', 
        ]);

        $gamer = Gamer::find($id);
        //if gamer does not exist
        if(!$gamer) {
            return response()->json([
                'status' => false,
                'message' => 'Gamer not found',
            ],404);    
        }
        
        try {
            $gamer->update([
                'gamer_preference' => $request->gamer_preference,
            ]);

            return response()->json([
                'message' => "Gamer account has been updated successfully.",
                'status' => true,
            ], 200);

        } catch (Exception $error) {
            return response()->json([
                'status' => false,
                'message' => "Error {$error->getMessage()}",
            ],500);
        }
    }
}
