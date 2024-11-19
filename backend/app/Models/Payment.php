<?php

namespace App\Models;

use App\Observers\PaymentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([PaymentObserver::class])]
class Payment extends Model
{
    use HasFactory;

    protected $table = "payments";

    protected $fillable = [
        'amount',
        'method',
        'details',
        'transaction_id',
        'payment_link',
        'status',
        'payer_id',
        'booking_id',
    ];

    //relationships
    public function transactionHistory()
    {
        return $this->hasMany(TransactionHistory::class,'payment_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function booking(){
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
