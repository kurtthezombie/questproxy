<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Booking;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ListingService
{
    protected $service;
    protected $booking;

    public function __construct(Service $service, Booking $booking){
        $this->service = $service;
        $this->booking = $booking;
    }

    public function index()
    {
        // Fetch only the necessary fields from the related models
        $services = $this->service->with([
            'pilot' => function ($query) {
                $query->select('id', 'user_id');  // Select only id and user_id from pilot
            },
            'pilot.user' => function ($query) {
                $query->select('id', 'username');  // Select only id and username from user
            }
        ])->get();

        // Add the pilot_username to each service
        $servicesWithUsername = $services->map(function ($service) {
            // Add the username directly to the service
            $service->pilot_username = $service->pilot->user->username;
            // Now remove the nested 'pilot' and 'user' to only return what is needed
            unset($service->pilot);
            return $service;
        });

        return $servicesWithUsername;
    }

    public function show($id) {
        return $this->service->findOrFail($id);
    }

    public function search($query) {
        return Service::search($query)->paginate();
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
            'category_id' => $data['category_id'],
        ]);

        if(!$service){
            throw new Exception('Failed to create instruction.');
        }

        return $service;
    }

    public function update($data,$id) {
        //find service
        $service = $this->service->findOrFail($id);
        $result = $service->update($data);
        if(!$result) {
            throw new Exception("Failed to update service.");
        }

        //return
        return $service;
    }

    public function destroy($id) {
        $service = $this->service->findOrFail($id);

        $hasActiveBookings = $this->booking->where('service_id', $service->id)
            ->whereNotIn('status', ['completed','cancelled'])
            ->exists();

        if ($hasActiveBookings) {
            throw new Exception('Cannot delete service with active bookings (pending or in-progress).',500);
        }

        if(!$service->delete()) {
            throw new Exception("Failed to delete service {$id}.");
        }

        return true;
    }

    public function serviceByPilot($pilot_id){
        $services = $this->service->with([
            'pilot' => function ($query) {
                $query->select('id', 'user_id');  // Select only id and user_id from pilot
            },
            'pilot.user' => function ($query) {
                $query->select('id', 'username');  // Select only id and username from user
            }
        ])
        ->where('pilot_id', $pilot_id)
        ->get();
    
        // Add the pilot_username to each service
        $servicesWithUsername = $services->map(function ($service) {
            // Add the username directly to the service
            $service->pilot_username = $service->pilot->user->username;
            // Now remove the nested 'pilot' and 'user' to only return what is needed
            unset($service->pilot);
            return $service;
        });
    
        return $servicesWithUsername;
    }

    public function getServiceByCategory($category_id)
    {
        return $this->service->where('category_id', $category_id)->get();
    }

    public function getServiceDetails($id)
    {
        $service = $this->service->with([
            'category',
            'pilot.user',
        ])->findOrFail($id);

        $clientUsername = auth()->user()->username;

        $details = [
            'description' => $service->description,
            'category_title' => $service->category->title,
            'pilot_username' => $service->pilot->user->username,
            'client_username' => $clientUsername,
            'price' => $service->price,
            'duration' => $service->duration,
        ];

        return $details;
    }
}

