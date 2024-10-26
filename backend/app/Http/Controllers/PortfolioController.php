<?php

namespace App\Http\Controllers;

use App\Models\Pilot;
use App\Models\Portfolio;
use App\Traits\ApiResponseTrait;
use Auth;
use Exception;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    use ApiResponseTrait;
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
            return $this->failedResponse('Pilot not found.',404);
        }
        try {
            //create portfolio
            $portfolio = Portfolio::create([
                'p_content' => $request->p_content,
                'pilot_id' => $pilot->id,
            ]);
            
            return $this->successResponse(
                'Portfolio successfully created.',
                201,
            );
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(),500);
        }   
    }

    /**
     * Display the specified portfolios of pilot
     * Takes pilot_id of the page
     */
    public function show($pilot_id)
    {
        $portfolios = Portfolio::where('pilot_id', $pilot_id)->get();
        
        return (!$portfolios) 
        ? $this->failedResponse('Portfolios not found.', 404) 
        : $this->successResponse('Portfolios successfully retrieved.',200,['portfolios' => $portfolios]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $portfolio = Portfolio::find($id);

        return !$portfolio
        ? $this->failedResponse("Portfolio {$id} not found.",404)
        : $this->successResponse('Portfolio successfully found.',200,['portfolio' => $portfolio]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate(['p_content'=>'required|string']);
        
        $portfolio = Portfolio::find($id);

        if (!$portfolio) {
            return $this->failedResponse("Portfolio {$id} not found.",404);
        }
        //update p_content
        $portfolio->p_content = $request->p_content;
        //save changes
        $portfolio->save();

        return $this->successResponse('Portfolio record successfully updated.',200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $deleted = Portfolio::destroy($id);
        
        return (!$deleted)
        ? $this->failedResponse('Portfolio deletion unsuccesful.',500)
        : $this->successResponse("Portfolio {$id} successfully deleted.",200);
    }

    public function destroyAll() {
        //find pilot id
        $user_id = Auth::user()->id;
        $pilot = Pilot::where('user_id',$user_id)->first();
        //delete portfolios with pilot id
        $deleted = Portfolio::where('pilot_id', $pilot->id)->delete();

        return (!$deleted)
        ? $this->failedResponse("Failed to delete portfolios of pilot {$pilot->id}.",500)
        : $this->successResponse("Portfolios successfully deleted.",200);
    }
}
