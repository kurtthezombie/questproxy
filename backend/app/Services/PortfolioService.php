<?php

namespace App\Services;

use App\Models\Pilot;
use App\Models\Portfolio;
use App\Models\User;
use Auth;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PortfolioService {
    protected $portfolio;
    protected $user;

    public function __construct(Portfolio $portfolio, User $user){
        $this->portfolio = $portfolio;
        $this->user = $user;
    }

    public function create(array $data){
        $pilot = $this->getPilotByUserId();
        try {
            if (isset($data['p_content'])) {
                $imagePath = $data['p_content']->store('portfolios', 'public');
                $data['p_content'] = $imagePath;
            }
        } catch (Exception $e) {
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

    public function update($data, $id)
    {
        $portfolio = $this->portfolio->findOrFail($id);

        DB::beginTransaction();
        try {
            if (isset($data['p_content']) && $data['p_content'] instanceof UploadedFile) {
                //delete old image
                if ($portfolio->p_content) {
                    Storage::disk('public')->delete($portfolio->p_content);
                }

                $imagePath = $data['p_content']->store('portfolios', 'public');
                $portfolio->p_content = $imagePath;
            }

            $portfolio->caption = $data['caption'];

            if (!$portfolio->save()) {
                throw new Exception("Failed to update portfolio record.");
            }

            DB::commit();
            return $portfolio;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
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

    public function getPortfolioByUser($user_id){
        $pilot = Pilot::where('user_id', $user_id)->firstOrFail();
        return $this->portfolio->where('pilot_id', $pilot->id)->get();
    }

    public function getPointsByUsername($username) {
        $user = $this->user->where('username', $username)->firstOrFail();

        $pilot = $user->pilot;

        $rank = $pilot->rank;

        $points = $rank->points;

        return $points;
    }

    private function getPilotByUserId()
    {
        $user_id = Auth::user()->id;
        return Pilot::where('user_id', $user_id)->firstOrFail();
    }

}
