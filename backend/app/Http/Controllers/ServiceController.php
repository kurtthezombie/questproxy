<?php

namespace App\Http\Controllers;

use App\Models\Pilot;
use App\Models\Service;
use Auth;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    //
    public function index()
    {
        $services = Service::all();
        return response()->json($services,200);
    }

    public function show($service_id)
    {
        try
        {
            $service = Service::find($service_id);

            if ($service)
            {
                return response()->json($service,200);
            }
            else
            {
                return response()->json("Service listing {$service_id} not found.",404);
            }
        }
        catch (\Exception $e)
        {
            return response()->json($e->getMessage(),500);
        }

    }
    public function store(Request $request)
    {
        //require validate
        $request->validate([
            'game' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric||min:0',
            'duration' => 'required|date',
            'availability' => 'required|date',
            'service_timestamp' => 'required|date',
        ]);
        //get pilot id by auth::user()->id
        $pilot_id = $this->getPilot();
        //::create
        try {
            $service = Service::create([
                'game' => $request->game,
                'description' => $request->description,
                'price' => $request->price,
                'duration' => $request->duration,
                'availability' => $request->availability,
                'service_timestamp' => $request->service_timestamp,
                'pilot_id' => $pilot_id,
            ]);
            //return response
            if ($service) {
                return response()->json([
                    'message' => "Listing {id} has been created.",
                ], 201);
            }
        } catch (\Exception $e) {
            //throw $th;
            return response()->json($e->getMessage());
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
            'price' => 'required|numeric||min:0',
            'duration' => 'required|date',
            'availability' => 'required|date',
            'service_timestamp' => 'required|date',
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
                'service_timestamp' => $request->service_timestamp,
            ]);
            //response
            if ($service)
            {
                return response()->json("Updated listing {$service->id} successfully",200);
            }
            else {
                return response()->json("Updating listing {$service->id} unsuccessful.",500);
            }
        }
        catch (\Exception $e)
        {
            return response()->json($e->getMessage());
        } 
    }

    public function destroy($service_id)
    {
        try{
            $deleted = Service::destroy($service_id);
            
            if ($deleted)
            {
                return response()->json("Service Listing {$service_id} deleted successfully",204);
            }
            else {
                return response()->json("Deletion failed", 500);
            }
        }
        catch(\Exception $e) {
            return response()->json($e->getMessage());
        }
    } 
    //
    public function getPilot()
    {
        $id = Auth::user()->id;
        $pilot_id = Pilot::where('user_id',$id)->first()->id;

        return $pilot_id;
    }

}
