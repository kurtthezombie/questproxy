<?php

namespace App\Http\Controllers;

use App\Models\Pilot;
use App\Traits\ApiResponseTrait;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;

class PilotController extends Controller
{
    //
    use ApiResponseTrait;

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
            return $this->failedResponse('Failed to retrieve pilots.',404);
        }
        return $this->successResponse('All pilots retrieved.',200,['pilots' => $pilots]);
    }

    public function show($id) {
        try{
            $pilot = $this->getPilot($id,"pilot_id");

            if (!$pilot) {
                return $this->failedResponse("Pilot record not found.",404);
            } 
            return $this->successResponse("Pilot record retrieved.", 200, ['pilot' => $pilot]);
        } catch (Exception $error) {
            return $this->failedResponse($error->getMessage(),500);
        }
    }
    public function edit($id)
    {
        try {
            //get pilot record
            $pilot = $this->getPilot($id, "pilot_id");

            if (!$pilot) {
                return $this->failedResponse('Pilot record not found.',404);
            }
            //return data and fill up the forms
            return $this->successResponse('Pilot record retrieved.',200,['pilot' => $pilot]);

        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(),500);
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
        
        if (!$pilot) {
            return $this->failedResponse('Pilot record cannot be retrieved',404);
        }

        try {
            //replace them with the request
            $pilot->update([
                'skills' => $request->skills,
                'bio' => $request->bio
            ]);

            return $this->successResponse("Pilot has been updated successfully.",200);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(),500);
        }

    }

}
