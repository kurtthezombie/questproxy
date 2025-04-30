<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = "reviews";

    protected $fillable = [
        'rating',
        'comment',
        'service_id',
        'pilot_id',
        'user_id',
    ];

    public function service(){
        return $this->belongsTo(Service::class);
    }
    public function pilot(){
        return $this->belongsTo(Pilot::class);
    }
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
