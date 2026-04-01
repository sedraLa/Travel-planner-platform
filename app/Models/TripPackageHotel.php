<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripPackageHotel extends Model
{
    use HasFactory;

     protected $fillable = [
        'trip_package_id',
        'hotel_id',
        'room_type',
        'amenities',
        'meal_plan'
    ];

    protected $casts = [
        'amenities' => 'array', //  JSON
    ];


    public function tripPackage()
    {
        return $this->belongsTo(TripPackage::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
