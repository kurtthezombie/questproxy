<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pilot extends Model
{
    use HasFactory;

    protected $fillable = [
        'skills',
        'bio',
        'user_id',
        'rank_id',
    ];

    //a pilot should be a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    //a pilot has many services
    public function service()
    {
        return $this->hasMany(Service::class);
    }

    public function portfolios() {
        return $this->hasMany(Portfolio::class, 'pilot_id');
    }
}
