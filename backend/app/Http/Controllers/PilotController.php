<?php

namespace App\Http\Controllers;

use App\Models\Pilot;
use Auth;
use DB;
use Exception;
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

    #portfolio functions
    public function createPortfolio(Request $request)
    {
        $user_id = Auth::user()->id;

        $pilot = Pilot::where('user_id', $user_id)->first();

        $request->validate([
            'p_content' => 'required|string',
        ]);

        $portfolio = DB::table('portfolios')->insert([
            'p_content' => $request->p_content,
            'pilot_id' => $pilot->id,
        ]);

        if ($portfolio) {
            return response()->json([
                'message' => 'Portfolio successfully added',
                'portfolio' => $portfolio,
            ], 201);
        } else {
            return response()->json([
                'message' => 'Error occurred during portfolio record insertion.'
            ], 500);
        }
    }
    public function showPortfolio($id)
    {
        $portfolios = DB::table('portfolios')
            ->where('pilot_id', $id)
            ->get();

        return response()->json($portfolios,200);
    }
    
    public function editPortfolio(Request $request, int $id)
    {
        //$id is for the portfolio item id
        $request->validate([
            'p_content' => 'required|string'
        ]);

        $updated = DB::table('portfolios')
            ->where('id', $id)
            ->update([
                'p_content' => $request->p_content
            ]);
        if ($updated) {
            return response()->json([
                'message' => 'Portfolio successfully edited',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Error occurred during portfolio record editing.'
            ], 500);
        }
    }

    public function destroyPortfolio(int $id)
    {
        $portfolio_item = DB::table('portfolios')->where('id',$id)->delete();

        if ($portfolio_item)
        {
            return response()->json([
                'message' => 'Portfolio item deleted.'
            ],200);
        }
        else {
            return response()->json([
                'message' => 'Portfolio item deletion unsuccessful.'
            ],500);
        }
    }
    public function destroyAllPortfolio(int $id)
    {
        $portfolios = DB::table('portfolios')->where('pilot_id',$id)->delete();

        if ($portfolios) 
        {
            return response()->json([
                'message' => 'Portfolios associated with user {$id} has been deleted.',
            ],200);
        }
        else
        {
            return response()->json([
                'message' => "Portfolios deletion unsuccessful",
            ],400);
        }
    }
}
