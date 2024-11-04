<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'service_id',
    ];

    //relationships
    public function transactionHistories()
    {
        return $this->hasMany(TransactionHistory::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function service(){
        return $this->belongsTo(Service::class, 'service_id');
    }
}
