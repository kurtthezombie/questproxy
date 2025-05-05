<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;

use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[ObservedBy([UserObserver::class])]
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

    public function pilot()
    {
        return $this->hasOne(Pilot::class, 'user_id');
    }

    public function gamer()
    {
        return $this->hasOne(Gamer::class);
    }

    public function reportsMade()
    {
        return $this->hasMany(Report::class, 'reporting_user_id');
    }

    public function reportsReceived()
    {
        return $this->hasMany(Report::class, 'reported_user_id');  
    }

    public function preference()
    {
        return $this->hasOne(Preference::class);
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

}
