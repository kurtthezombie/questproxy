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

    public function update($data, $id) {
        $gamer = $this->gamer->findOrFail($id);

        $gamer->update(['gamer_preference' => $data['gamer_preference']]);

        return $gamer;
    }
}
