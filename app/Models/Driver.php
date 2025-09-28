<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'age',
        'address',
        'license_image',
        'experience',
        'email',
        'phone',
        'license_category',
    ];

    // علاقة One-to-One مع السيارة
    public function vehicle()
    {
        return $this->hasOne(Vehicle::class);
    }

    // علاقة One-to-Many مع الحجوزات مباشرة
    public function reservations()
    {
        return $this->hasMany(TransportReservation::class);
    }
}
