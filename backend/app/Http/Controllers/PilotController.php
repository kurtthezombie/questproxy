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
    public function getPilot(int $id, string $type)
    {
        if ($type === "pilot_id"){
            return Pilot::find($id);   
        }
        else {
            return Pilot::where('user_id',$id)->first();
        }
    }

    public function edit($id)
    {
        //get pilot record
        $pilot = $this->getPilot($id,"pilot_id");

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
        $pilot = $this->getPilot($id,"pilot_id");

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

        $pilot = $this->getPilot($user_id,"user_id");

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
    //takes user_id to get pilot_id to get portfolios
    public function showPortfolio($id)
    {
        $pilot = $this->getPilot($id,"user_id");

        if ($pilot) 
        {
            $portfolios = DB::table('portfolios')
            ->where('pilot_id', $pilot->id)
            ->get();

            return response()->json($portfolios,200);
        }
        else
        {
            return response()->json([
                'pilot' => $pilot,
                'message' => 'Pilot record was not found'
            ],404);
        }
        
    }
    
    public function editPortfolio(Request $request, int $id)
    {
        //$id is for the portfolio item id
        $request->validate([
            'p_content' => 'required|string'
        ]);
        try {
            $updated = DB::table('portfolios')
                ->where('id', $id)
                ->update([
                    'p_content' => $request->p_content
                ]);
            if ($updated) {
                return response()->json([
                    'message' => 'Portfolio successfully edited',
                    'p_content' => $request->p_content,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Error occurred during portfolio record editing.'
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
        
    }

    public function destroyPortfolio(int $id)
    {
        try 
        {
            $portfolio_item = DB::table('portfolios')->where('id', $id)->first();

            if (!$portfolio_item) {
                return response()->json([
                    'message' => "Portfolio item {$id} not found."
                ], 404);
            }
            
            DB::table('portfolios')->where('id',$id)->delete();

            return response()->json([
                'message' => "Portfolio item with ID {$id} has been deleted",
            ],200);
        } 
        catch (Exception $e) 
        {
            return response()->json([
                'message' => "An error occurred while deleting the portfolio item.",
                'error' => $e->getMessage(),
            ], 500);
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
