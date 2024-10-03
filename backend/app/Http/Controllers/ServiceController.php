<?php

namespace App\Http\Controllers;

use App\Models\Pilot;
use App\Models\Service;
use Auth;
use Exception;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    //
    public function index()
    {
        try {
            $services = Service::all();
            return response()->json([
                'services' => $services,
                'message' => 'All services retrieved.',
                'status' => true,
            ],200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false,
            ],500);
        }
    }

    public function show($service_id)
    {
        try
        {
            $service = Service::find($service_id);

            if ($service)
            {
                return response()->json([
                    'service' => $service,
                    'message' => 'Service listing retrieved.',
                    'status' => true,
                ],200);
            }
            else
            {
                return response()->json([
                    'message' => "Service listing {$service_id} not found.",
                    'status' => false,
                ],404);
            }
        }
        catch (Exception $e)
        {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false,
            ],500);
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
        if ($pilot_id) {
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
                        'status' => true,
                    ], 201);
                }
            } catch (\Exception $e) {
                //throw $th;
                return response()->json([
                    'message' => $e->getMessage(),
                    'status' => false,
                ],500);
            }
        }
        else
        {
            return response()->json([
                'message' => 'Pilot record was not found.',
                'status' => false,
            ],404);
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
                return response()->json([
                    'message' => "Updated listing {$service->id} successfully",
                    'status' => true,
                ],200);
            }
            else {
                return response()->json([
                    'message' => "Updating listing {$service->id} unsuccessful.",
                    'status' => false,
                ],500);
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
                return response()->json([
                    'message' => "Service Listing {$service_id} deleted successfully",
                    'status' => true,
                ],204);
            }
            else {
                return response()->json([
                    'message' => "Deletion failed",
                    'status' => false,
                ], 500);
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
        try {
            $pilot_id = Pilot::where('user_id',$id)->first()->id;
            return $pilot_id;
        } catch (\Exception $e) {
            return null;
        }
    }

}
