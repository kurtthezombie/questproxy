<?php

namespace App\Http\Controllers;

use App\Models\Pilot;
use App\Models\Service;
use App\Services\ListingService;
use App\Traits\ApiResponseTrait;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use ApiResponseTrait;
    protected $listingService;  

    public function __construct(ListingService $listingService){
        $this->listingService = $listingService;
    }

    public function search(Request $request) {
        $query = $request->validate([
            'search' => 'nullable|string'
        ]);
        
        try {
            $services = $this->listingService->search($query);
            $message = $services->isEmpty() ? 'Services are empty' : 'Here are the search results';
            
            //return success
            return $this->successResponse($message,200,['services' => $services]);
        } catch (Exception $error) {
            return $this->failedResponse("Error {$error->getMessage()}",400);
        }
    }
    
    public function index()
    {
        try {
            $services = $this->listingService->index();
            $message = $services->isEmpty() ? 'Services are empty.' : 'All services retrieved.';

            //if there are service listings
            return $this->successResponse($message,200,['services' => $services]);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(),500);
        }
    }

    public function show($service_id)
    {
        try
        {   
            $service = Service::find($service_id);
    
            return $this->successResponse('Service listing retrieved.',200,['service' => $service]);
        }
        catch (ModelNotFoundException $e) {
            return $this->failedResponse("Service listing {$service_id} not found.", 404);
        }
        catch (Exception $e)
        {
            return $this->failedResponse($e->getMessage(),500);
        }
    }

    public function store(Request $request)
    {
        //require validate
        $data = $request->validate([
            'game' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|date',
            'availability' => 'required|boolean',
        ]);
        //get pilot id by auth::user()->id
        $pilot_id = $this->getPilot();
        
        if (!$pilot_id){
            return $this->failedResponse('Pilot record was not found.',404);
        }

        try {
            $service = $this->listingService->create($data, $pilot_id);

            return $this->successResponse("Listing {$service->id} has been created.", 201);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(), 500);
        }
    }

    public function edit($service_id)
    {
        return $this->show($service_id);
    }

    public function update(Request $request,$service_id)
    {
        //request validate
        $data = $request->validate([
            'game' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|date',
            'availability' => 'required|boolean',
        ]);

        //update
        try
        {
            $this->listingService->update($data,$service_id);

            return $this->successResponse("Updated listing {$service_id} successfully",200);
        }
        catch (ModelNotFoundException $e) {
            return $this->failedResponse("Error: " . $e->getMessage(), 404);
        } 
        catch (Exception $e)
        {
            return $this->failedResponse("Error: " . $e->getMessage(), 500);
        }
    }

    public function destroy($service_id)
    {
        try{
            $this->listingService->destroy($service_id);
            
            return $this->successResponse("Service {$service_id} deleted successfully.",200);
        }
        catch (ModelNotFoundException $e) {
            return $this->failedResponse("Error: " . $e->getMessage(),404);
        }
        catch(Exception $e) {
            return $this->failedResponse("Error: " . $e->getMessage(),500);
        }
    }

    public function getServicesByPilot($pilot_id){
        try {
            $services = $this->listingService->serviceByPilot($pilot_id);
            $message = $services->isEmpty() ? 'No services found for this pilot.' : 'All services retrieved.';
            
            return $this->successResponse($message,200,['services' => $services]);
        } catch (QueryException $e) { // Specific exception for DB errors
            return $this->failedResponse("Database error: " . $e->getMessage(), 500);
        } 
        catch (Exception $e){
            return $this->failedResponse("Error: " . $e->getMessage(),500);
        }
    }

    //
    public function getPilot()
    {
        $id = Auth::id(); // Shortcut for Auth::user()->id

        return Pilot::where('user_id', $id)->value('id'); // Directly fetch the 'id' column value
    }

}
