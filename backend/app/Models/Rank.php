<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    use HasFactory;

    protected $table = 'ranking';

    protected $fillable = [
        'pilot_rank',
        'points'
    ];

    public function pilots()
    {
        return $this->hasMany(Pilot::class, 'rank_id');
    }
}
