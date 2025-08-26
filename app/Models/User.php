<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Favorite;
use App\Models\Destination;
use App\Models\Hotel;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'role'              => 'string',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id', 'id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'user_id', 'id');
    }

    // كل المفضلات
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    // وجهات مفضلة
    public function favoriteDestinations()
    {
        return $this->morphedByMany(Destination::class, 'favoritable', 'favorites');
    }

    // فنادق مفضلة
    public function favoriteHotels()
    {
        return $this->morphedByMany(Hotel::class, 'favoritable', 'favorites');
    }
}
