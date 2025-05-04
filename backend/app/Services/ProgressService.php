<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ProgressLog;
use App\Notifications\ProgressItemAddedNotification;
use Exception;

class ProgressService
{
      protected $booking;
      protected $progressLog;

      public function __construct(Booking $booking, ProgressLog $progressLog)
      {
            $this->booking = $booking;
            $this->progressLog = $progressLog;
      }

      public function create(array $data)
      {
            $imagePath = $data['image']->store('progress_updates', 'public');
            
            $created = $this->progressLog->create([
                  'image_path' => $imagePath,
                  'description' => $data['description'],
                  'booking_id' => $data['booking_id'],
            ]);

            if (!$created) {
                  throw new Exception("Failed to add progress item.");
            }

            // Load the booking with its relationships
            $booking = $this->booking->with(['client'])->findOrFail($data['booking_id']);
            
            // Send notification to the client
            $this->sendProgressNotification($booking, $created);

            return $created;
      }

      private function sendProgressNotification($booking, $progressLog)
      {
            $booking->client->notify(new ProgressItemAddedNotification($booking, $progressLog));
      }

      public function getProgressByBooking($bookingId)
      {
            $records = $this->progressLog
                        ->where('booking_id', $bookingId)
                        ->orderBy('created_at', 'desc')
                        ->get();
            return $records;
      }

      public function delete($id)
      {
            $progress = $this->progressLog->findOrFail($id);

            if(!$progress->delete()){
                  throw new Exception('Failed to delete progress item');
            }

            return true;
      }
}