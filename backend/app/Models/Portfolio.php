<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    protected $table = 'portfolios';

    protected $fillable = [
        'p_content',
        'caption',
        'pilot_id',
    ];

    public $timestamps = false;

    public function pilot() {
        return $this->belongsTo(Pilot::class, 'pilot_id');
    }
}
