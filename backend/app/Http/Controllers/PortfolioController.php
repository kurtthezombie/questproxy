<?php

namespace App\Http\Controllers;

use App\Models\Pilot;
use App\Models\Portfolio;
use App\Services\PortfolioService;
use App\Traits\ApiResponseTrait;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    use ApiResponseTrait;

    protected $portfolioService;

    public function __construct(PortfolioService $portfolioService){
        $this->$portfolioService = $portfolioService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'p_content' => 'required|string',
        ]);

        try {
            //create portfolio
            $portfolio = $this->portfolioService->create($data);

            return $this->successResponse('Portfolio successfully created.',201,['portfolio' => $portfolio]);
        } catch (ModelNotFoundException $e) {
            return $this->failedResponse("Error: " . $e->getMessage(),404);
        } catch (Exception $e) {
            return $this->failedResponse("Error: " . $e->getMessage(),500);
        }
    }

    /**
     * Display the specified portfolios of pilot
     * Takes pilot_id of the page
     */
    public function show($pilot_id)
    {
        try {
            $portfolios = $this->portfolioService->findByPilot($pilot_id);

            $message = $portfolios->isEmpty()
            ? "Pilot has no portfolio items yet."
            : "Portfolios successfully retrieved.";

            return $this->successResponse($message, 200, ['portfolios' => $portfolios]);
        } catch(Exception $e) {
            return $this->failedResponse("Error: " . $e->getMessage(), 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $portfolio = $this->portfolioService->findPortfolio($id);

            return $this->successResponse("Portfolio successfully retrieved.",200,['portfolio' => $portfolio]);
        } catch (ModelNotFoundException $e) {
            return $this->failedResponse("Error: " . $e->getMessage(), 404);
        } catch (Exception $e) {
            return $this->failedResponse("Error: " . $e->getMessage(),500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate(['p_content'=>'required|string']);

        try {
            $this->portfolioService->update($data,$id);

            return $this->successResponse("Portfolio record {$id} successfully updated.",200);
        } catch (ModelNotFoundException $e) {
            return $this->failedResponse("Portfolio {$id} not found.", 404);
        } catch (Exception $e){
            return $this->failedResponse("Error: " . $e->getMessage(),500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->portfolioService->delete($id);

            return $this->successResponse("Successfully deleted portfolio {$id}.", 200);
        } catch (ModelNotFoundException $e) {
            return $this->failedResponse("Portfolio {$id} not found.",404);
        } catch (Exception $e){
            return $this->failedResponse("Error: " . $e->getMessage(),500);
        }
    }

    public function destroyAll() {
        try {
            //find pilot id
            $user_id = Auth::user()->id;
            $pilot = Pilot::where('user_id',$user_id)->first();

            $this->portfolioService->deleteAll($pilot->id);

            return $this->successResponse("Portfolios of {$pilot->id} successfully deleted.",200);
        }  catch (ModelNotFoundException $e) {
            return $this->failedResponse("Error: " . $e->getMessage(), 404);
        } catch (Exception $e){
            return $this->failedResponse("Error: " . $e->getMessage(),500);
        }
    }
}
