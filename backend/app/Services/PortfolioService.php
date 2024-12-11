<?php

namespace App\Services;

use App\Models\Pilot;
use App\Models\Portfolio;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PortfolioService {
    protected $portfolio;

    public function __construct(Portfolio $portfolio){
        $this->portfolio = $portfolio;
    }

    public function create($data){
        //retrieve pilot thru user id
        $user_id = Auth::user()->id;

        //retrieve pilot associated with the authenticated user
        $pilot = Pilot::where('user_id',$user_id)->first();

        if(!$pilot){
            throw new ModelNotFoundException("Pilot not found.");
        }

        $created = $this->portfolio->create([
            'p_content' => $data['p_content'],
            'pilot_id' => $pilot->id,
        ]);

        if (!$created) {
            throw new Exception("Failed to create portfolio record.");
        }

        return $created;
    }

    public function findByPilot($pilot_id){
        return $this->portfolio->where('pilot_id', $pilot_id)->get();
    }

    public function findPortfolio($id){
        return $this->portfolio->findOrFail($id);
    }

    public function update($data,$id){
        $portfolio = $this->portfolio->findOrFail($id);

        //update p_content
        $portfolio->p_content = $data['p_content'];

        if(!$portfolio->save()){
            throw new Exception("Failed to update portfolio record.");
        }

        return true;
    }

    public function delete($id){
        $portfolio = $this->portfolio->findOrFail($id);

        if(!$portfolio->delete()){
            throw new Exception("Failed to delete portfolio.");
        }

        return true;
    }

    public function deleteAll($pilot_id){
        $deleted = $this->portfolio->where('pilot_id',$pilot_id)->delete();

        if ($deleted === 0) {
            throw new ModelNotFoundException("No portfolios found for pilot {$pilot_id}.");
        }

        return true;
    }
}
