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
        try {
            if ($type === "pilot_id"){
                return Pilot::find($id);
            }
            else {
                return Pilot::where('user_id',$id)->first();
            }
        } catch (Exception $e) {
            return null;
        }

    }

    public function index() {
        $pilots = Pilot::all();

        if (!$pilots) {
            return response()->json([
                'message' => 'Failed to retrieve pilots.',
                'status' => false,
            ],404);
        }

        return response()->json([
            'pilots' => $pilots,
            'message' => 'All pilots retrieved.',
            'status' => true
        ],200);
    }

    public function show($id) {
        try{
            $pilot = $this->getPilot($id,"pilot_id");

            if (!$pilot) {
                return response()->json([
                    'message' => "Pilot record not found.",
                    'status' => false,
                ],404);
            } 

            return response()->json([
                'pilot' => $pilot,
                'message' => "Pilot record retrieved.",
                'status' => true,
            ],200); 
        } catch (Exception $error) {
            return response()->json([
                'message' => $error->getMessage(),
                'status' => false,
            ],500);
        }
    }
    public function edit($id)
    {
        try {
            //get pilot record
            $pilot = $this->getPilot($id, "pilot_id");

            if ($pilot) {
                //return data and fill up the forms
                return response()->json([
                    'pilot' => $pilot,
                    'message' => 'Pilot record retrieved.',
                    'status' => true,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Pilot record not found.',
                    'status' => false,
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false,
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
            try {
                //replace them with the request
                $pilot->update([
                    'skills' => $request->skills,
                    'bio' => $request->bio
                ]);

                return response()->json([
                    'message' => "Pilot has been updated successfully.",
                    'status' => true,
                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    'message'=> $e->getMessage(),
                    'status'=> false,
                ]);
            }

        }
        else {
            return response()->json([
                'message' => 'Pilot record cannot be retrieved',
                'status' => false,
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
                'status' => true,
            ], 201);
        } else {
            return response()->json([
                'message' => 'Error occurred during portfolio record insertion.',
                'status' => false,
            ], 500);
        }
    }

}
