<?php

namespace App\Http\Controllers;

use App\Models\Gamer;
use App\Services\GamerService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class GamerController extends Controller
{
    use ApiResponseTrait;

    protected $gamerService;

    public function __construct(GamerService $gamerService){
        $this->gamerService = $gamerService;
    }

    public function edit($id){
        try {
            $gamer = $this->gamerService->findById($id);

            return $this->successResponse("Gamer account {$id} successfully retrieved.",200, ['gamer' => $gamer]);
        } catch (ModelNotFoundException $e) {
            return $this->failedResponse("Gamer {$id} not found", 404);
        } catch (Exception $e) {
            return $this->failedResponse("Error: " . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id) {
        $data = $request->validate([
            'gamer_preference' => 'required|string',
        ]);

        try {
            $gamer = $this->gamerService->update($data,$id);

            return $this->successResponse("Gamer account has been updated successfully.",200,['gamer' => $gamer]);
        } catch (ModelNotFoundException $e) {
            return $this->failedResponse("Gamer {$id} not found", 404);
        } catch (Exception $e) {
            return $this->failedResponse("Error " . $e->getMessage(),500);
        }
    }
}
