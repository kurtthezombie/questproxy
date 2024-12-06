<?php

namespace App\Http\Controllers;

use App\Models\Pilot;
use App\Models\Service;
use App\Traits\ApiResponseTrait;
use Auth;
use Exception;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    //
    use ApiResponseTrait;

    public function search(Request $request) {
        try {
            $services = Service::search($request->search)->paginate();
            if (!$services) {
                return $this->successResponse('Services are empty', 200);
            }
            //return success
            return $this->successResponse('Here are the search results...',200,['services' => $services]);
        } catch (Exception $error) {
            return $this->failedResponse("Error {$error->getMessage()}",400);
        }
    }

    public function index()
    {
        try {
            $services = Service::all();
            if (!$services) {
                return $this->successResponse('Services are empty',200);
            }
            //if there are service listings
            return $this->successResponse('All services retrieved.',200,['services' => $services]);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(),500);
        }
    }

    public function show($service_id)
    {
        try
        {
            $service = Service::find($service_id);
            
            if (!$service) {
                return $this->failedResponse("Service listing {$service_id} not found.",404);
            }

            return $this->successResponse('Service listing retrieved.',200,['service' => $service]);
        }
        catch (Exception $e)
        {
            return $this->failedResponse($e->getMessage(),500);
        }

    }
    public function store(Request $request)
    {
        //require validate
        $request->validate([
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
        //::create
        try {
            $service = Service::create([
                'game' => $request->game,
                'description' => $request->description,
                'price' => $request->price,
                'duration' => $request->duration,
                'availability' => $request->availability,
                'pilot_id' => $pilot_id,
            ]);
            //return response
            if ($service) {
                return $this->successResponse("Listing {$service->id} has been created.", 201);
            }
        } catch (Exception $e) {
            //throw $th;
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
        $request->validate([
            'game' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|date',
            'availability' => 'required|boolean',
        ]);
        //update
        try
        {
            $service = Service::find($service_id);
            $service->update([
                'game' => $request->game,
                'description' => $request->description,
                'price' => $request->price,
                'duration' => $request->duration,
                'availability' => $request->availability,
            ]);
            //response
            if (!$service)
            {
                return $this->failedResponse("Updating listing {$service->id} unsuccessful.",500);
            }
            return $this->successResponse("Updated listing {$service->id} successfully",200);
        }
        catch (Exception $e)
        {
            return response()->json($e->getMessage());
        }
    }

    public function destroy($service_id)
    {
        try{
            $deleted = Service::destroy($service_id);

            if (!$deleted)
            {
                return $this->failedResponse("Deletion failed",500);
            }
            else {
                return $this->successResponse("Service Listing {$service_id} deleted successfully",204);
            }
        }
        catch(Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function getServicesByPilot($pilot_id){
        $services = Service::where('pilot_id',$pilot_id)->get();

        if($services->isEmpty()) {
            return $this->successResponse('No services found for this pilot.',404);
        }

        return $this->successResponse('Services retrieved.',200,['services' => $services]);
    }

    //
    public function getPilot()
    {
        $id = Auth::user()->id;
        try {
            $pilot_id = Pilot::where('user_id',$id)->first()->id;
            return $pilot_id;
        } catch (Exception $e) {
            return null;
        }
    }

}
