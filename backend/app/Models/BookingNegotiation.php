<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingNegotiation extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'negotiable_price',
        'pilot_status',
        'final_price',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
