<?php

namespace App\Http\Controllers;

use App\Models\Pilot;
use App\Models\Portfolio;
use Auth;
use Exception;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //wont display all portfolios, need to be portfolios for specific users only
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'p_content' => 'required|string',
        ]);
        //retrieve pilot
        $user_id = Auth::user()->id;
        $pilot = Pilot::where('user_id',$user_id)->first();

        if(!$pilot) {
            return response()->json([
                'message' => 'Pilot not found.',
                'status' => false,
            ],404);
        }
        try {
            //create portfolio
            $portfolio = Portfolio::create([
                'p_content' => $request->p_content,
                'pilot_id' => $pilot->id,
            ]);

            return response()->json([
                'message' => "Portfolio successfully created.",
                'status' => true,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false,
            ],500);
        }   
    }

    /**
     * Display the specified resource.
     * Takes pilot_id of the page
     */
    public function show($pilot_id)
    {
        $portfolios = Portfolio::where('pilot_id', $pilot_id)->get();
        
        if(!$portfolios) {
            return response()->json([
                'message' => 'Portfolios not found.',
                'status' => false,
            ],404);
        }

        return response()->json([
            'portfolios' => $portfolios,
            'message' => 'Portfolios successfully retrieved.',
            'status' => true,
        ],200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $portfolio = Portfolio::find($id);

        if (!$portfolio) {
            return response()->json([
                'message' => "Portfolio {$id} not found.",
                'status' => false,
            ],404);
        }
        return response()->json([
            'portfolio' => $portfolio,
            'message' => 'Portfolio successfully found.',
            'status' => true,
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate(['p_content'=>'required|string']);
        
        $portfolio = Portfolio::find($id);

        if (!$portfolio) {
            return response()->json([
                'message' => "Portfolio {$id} not found.",
                'status' => false,
            ],404);
        }
        $portfolio->p_content = $request->p_content;

        $portfolio->save();

        return response()->json([
            'message' => 'Portfolio record successfully updated.',
            'status' => true,
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $deleted = Portfolio::destroy($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Portfolio deletion unsuccesful.',
                'status' => false,
            ],500);
        }

        return response()->json([
            'message' => "Portfolio {$id} successfully deleted.",
            'status' => true,
        ],200);
    }

    public function destroyAll() {
        //find pilot id
        $user_id = Auth::user()->id;
        $pilot = Pilot::where('user_id',$user_id)->first();
        //delete portfolios with pilot id
        $deleted = Portfolio::where('pilot_id', $pilot->id)->delete();

        if(!$deleted) {
            return response()->json([
                'message' => "Failed to delete portfolios of pilot {$pilot->id}.",
                'status' => false, 
            ]);
        }

        return response()->json([
            'message' => "Portfolios successfully deleted.",
            'status' => true,
        ]);
    }
}
