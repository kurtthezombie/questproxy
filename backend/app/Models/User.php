<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'f_name',
        'l_name',
        'contact_number',
        'role',
        'status'
    ];


    public function payments(){
        return $this->hasMany(Payment::class,'payer_id');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            //'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(){
        static::created(function ($user) {
            if($user->role == 'gamer'){
                Gamer::create([
                    'user_id' => $user->id
                ]);
            }
            elseif(in_array($user->role, ['game_pilot','game pilot'])){
                $rank = Rank::create();
                Pilot::create([
                    'user_id' => $user->id,
                    'rank_id' => $rank->id
                ]);
            }
        });
    }
}
