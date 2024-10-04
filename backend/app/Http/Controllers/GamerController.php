<?php

namespace App\Http\Controllers;

use App\Models\Gamer;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;

class GamerController extends Controller
{
    use ApiResponseTrait;
    public function edit($id){
        $gamer = Gamer::find($id);

        if (!$gamer) {
            return $this->failedResponse("Gamer account {$id} not found",404);
        }

        return $this->successResponse("Gamer account {$id} found.",200,['gamer' => $gamer]);
    }

    public function update(Request $request, $id) {
        $request->validate([
            'gamer_preference' => 'required|string', 
        ]);

        $gamer = Gamer::find($id);
        //if gamer does not exist
        if(!$gamer) {
            return $this->failedResponse('Gamer not found.',404);    
        }
        
        try {
            $gamer->update([
                'gamer_preference' => $request->gamer_preference,
            ]);

            return $this->successResponse("Gamer account has been updated successfully.",200);

        } catch (Exception $error) {
            return $this->failedResponse("Error {$error->getMessage()}",500);
        }
    }
}
