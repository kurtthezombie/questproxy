<?php 

namespace App\Services;

use App\Models\Preference;

class PreferenceService { 
      protected $preference;

      public function __construct(Preference $preference)
      {
            $this->preference = $preference;
      }

      public function create($data)
      {
            $existing = $this->preference->where('user_id', $data['user_id'])->first();

            if ($existing) {
                  throw new \Exception('User already has a preference.');
            }

            return $this->preference->create($data);
      }

      public function getUserPreference($userId)
      {
            $preference = $this->preference->where('user_id', $userId)->first();
            
            if ($preference) {
                  // Load the category relationship
                  $preference->load('category');
            }
            
            return $preference;
      }

      public function update($userId, $data)
      {
            $preference = $this->preference->where('user_id', $userId)->first();

            if (!$preference) {
                  throw new \Exception('Preference not found.');
            }

            $preference->update($data);

            return $preference;
      }
}