<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressLog extends Model
{
    protected $table = "progress_logs";

    protected $fillable = [
        'booking_id',
        'description',
        'image_path',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
