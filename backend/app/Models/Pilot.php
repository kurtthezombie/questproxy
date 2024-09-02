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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
