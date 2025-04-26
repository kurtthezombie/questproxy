<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instruction extends Model
{
    use HasFactory;

    protected $table = "booking_instructions";

    protected $fillable = [
        'booking_id',
        'start_date',
        'communication_link',
        'additional_notes',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
