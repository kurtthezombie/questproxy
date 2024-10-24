<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = "reports";

    protected $fillable = [
        'reason',
        'status',
        'reporting_user_id',
        'reported_user_id',
    ];

    public function reportedUser(){
        return $this->belongsTo(User::class,'reported_user_id');
    }

    public function reportingUser() {
        return $this->belongsTo(User::class, 'reporting_user_id');
    } 
}
