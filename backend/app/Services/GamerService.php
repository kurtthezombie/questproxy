<?php

namespace App\Services;

use App\Models\Gamer;

class GamerService {
    
    protected $gamer;

    public function __construct(Gamer $gamer) {
        $this->gamer = $gamer;
    }

    public function findById($id) {
        return $this->gamer->findOrFail($id);
    }
}