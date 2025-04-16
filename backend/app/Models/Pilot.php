<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Pilot extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'skills',
        'bio',
        'user_id',
        'rank_id',
    ];

    //a pilot should be a user
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function rank()
    {
        return $this->belongsTo(Rank::class,'rank_id');
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
