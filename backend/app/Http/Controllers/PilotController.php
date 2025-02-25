<?php

namespace App\Http\Controllers;

use App\Models\Pilot;
use App\Services\PilotService;
use App\Services\PilotServices;
use App\Traits\ApiResponseTrait;
use Auth;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PilotController extends Controller
{
    //
    use ApiResponseTrait;

    protected $pilotService;

    public function __construct(PilotService $pilotService){
        $this->pilotService = $pilotService;
    }

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
        try {
            $pilots = $this->pilotService->index();
            $message = $pilots->isEmpty() ? "No pilots yet." : "All pilots retrieved.";

            return $this->successResponse($message,200,['pilots' => $pilots]);
        } catch (Exception $e) {
            return $this->failedResponse('Failed to retrieve pilots: ' . $e->getMessage(),500);
        }
    }

    public function show($id) {
        try{
            $pilot = $this->pilotService->findById($id);

            return $this->successResponse("Pilot record {$id} retrieved.", 200, ['pilot' => $pilot]);
        } catch (ModelNotFoundException $e) {
            return $this->failedResponse("Pilot record {$id} not found.",404);
        } catch (Exception $error) {
            return $this->failedResponse("Error: " . $error->getMessage(),500);
        }
    }

    public function edit($id)
    {
        //just call the show function
        return $this->show($id);
    }

    public function update(Request $request, int $id)
    {
        //validate inputs
        $data = $request->validate([
            'skills' => 'required|string',
            'bio' => 'required|string',
        ]);

        try {
            $this->pilotService->update($data,$id);

            return $this->successResponse("Pilot has been updated successfully.",200);
        } catch(ModelNotFoundException $e) {
            return $this->failedResponse("Pilot {$id} is not found.",404);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(),500);
        }
    }
}
