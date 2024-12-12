<?php

namespace App\Services;

use App\Models\Service;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ListingService
{
    protected $service;

    public function __construct(Service $service){
        $this->service = $service;
    }

    public function index() {
        return $this->service->all();
    }

    public function show($id) {
        return $this->service->findOrFail($id);
    }

    public function search($query) {
        return $this->service->search($query)->paginate();
    }

    public function create($data, $pilot_id)
    {
        $service = $this->service->create([
            'game' => $data['game'],
            'description' => $data['description'],
            'price' => $data['price'],
            'duration' => $data['duration'],
            'availability' => $data['availability'],
            'pilot_id' => $pilot_id,
        ]);

        if(!$service){
            throw new Exception('Failed to create instruction.');
        }

        return $service;
    }

    public function update($data,$id) {
        //find service
        $service = $this->service->findOrFail($id);

        if(!$service->update($data)) {
            throw new Exception("Failed to create service.");
        }

        //return
        return $service;
    }

    public function destroy($id) {
        $service = $this->service->findOrFail($id);

        if(!$service->delete()) {
            throw new Exception("Failed to delete service {$id}.");
        }

        return true;
    }

    public function serviceByPilot($pilot_id){
        return $this->service->where('pilot_id', $pilot_id)->get();
    }
}
