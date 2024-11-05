<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    use HasFactory;

    protected $table = "transaction_history";

    protected $fillable = [
        'payment_id',
        'status'
    ];

    public function payment(){
        return $this->belongsTo(Payment::class,'payment_id'); 
    }
}
