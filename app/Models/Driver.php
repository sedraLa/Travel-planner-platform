<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'age',
        'address',
        'license_image',
        'license_category',
        'experience',
        /*'email',*/
        /*'phone',*/
    ];

    // علاقة One-to-One مع السيارة
    public function vehicle()
    {
        return $this->hasOne(TransportVehicle::class);
    }

    // علاقة One-to-Many مع الحجوزات مباشرة
    public function reservations()
    {
        return $this->hasMany(TransportReservation::class);
    }


     public function user()
    {
        return $this->belongsTo(User::class);
    }


}
