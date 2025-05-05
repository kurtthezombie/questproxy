<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    protected $table = 'preferences';

    protected $fillable = [
        'user_id',
        'game_id',
        'service',
        'points',
        'duration',
        'max_price',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'game_id');
    }
}
