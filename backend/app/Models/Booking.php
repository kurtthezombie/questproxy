<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = "bookings";

    protected $fillable = [
       'client_id',
       'service_id',
       'status', 
    ]; 
    //a booking has a client
    public function client(){
        return $this->belongsTo(User::class,'client_id');
    }
    //a booking belongs to a service
    public function service(){
        return $this->belongsTo(Service::class, 'service_id');
    }
    //a booking has an instruction
    public function instruction(){
        return $this->hasOne(Instruction::class,'booking_id');
    }
    //a booking has a payment
    public function payment(){
        return $this->hasOne(Payment::class);
    }
}
