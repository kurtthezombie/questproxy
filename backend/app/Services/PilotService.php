<?php

namespace App\Services;

use App\Models\Pilot;

class PilotService {

    protected $pilot;

    public function __construct(Pilot $pilot){
        $this->pilot = $pilot;
    }

    public function index(){
        return $this->pilot->all();
    }

    public function findById($id){
        return $this->pilot->findOrFail($id);
    }

    public function update($data,$id) {
        $pilot = $this->pilot->findOrFail($id);

        $pilot->update($data);

        return $pilot;
    }
}
