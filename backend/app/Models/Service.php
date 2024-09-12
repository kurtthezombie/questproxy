<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'game',
        'description',
        'price',
        'duration',
        'availability',
        'service_timestamp',
        'pilot_id'
    ];
}
