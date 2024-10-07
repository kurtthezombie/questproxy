<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Service extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'game',
        'description',
        'price',
        'duration',
        'availability',
        'service_timestamp',
        'pilot_id'
    ];

    public function pilot()
    {
        return $this->belongsTo(Pilot::class, 'pilot_id');
    }

    public function toSearchableArray():array 
    {
        return [
            'description' => $this->description,
            'game' => $this->game,
            //cant add related attributes because of db driver limitations
            //'pilot_name' => optional($this->pilot->user)->name ?? null,
        ];
    }
    
}
