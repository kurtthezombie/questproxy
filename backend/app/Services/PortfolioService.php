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

    public function create(array $data){
        $pilot = $this->getPilotByUserId();
        try {
            if (isset($data['p_content'])) {
                $imagePath = $data['p_content']->store('portfolios', 'public');
                $data['p_content'] = $imagePath;
            }
        } catch (Exception $e) {
            \Log::error("Image Upload Failed: " . $e->getMessage());
            return response()->json(['error' => 'Image upload failed'], 500);
        }
        

        $created = $this->portfolio->create([
            'p_content' => $data['p_content'],
            'caption' => $data['caption'],
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

        return $portfolio;
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

    private function getPilotByUserId()
    {
        $user_id = Auth::user()->id;
        return Pilot::where('user_id', $user_id)->firstOrFail();
    }
}
