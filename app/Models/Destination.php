<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'city',
        'country',
        'description',
        'location_details',
        'activities',
        'weather_info',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function images() {
        return $this->hasMany(DestinationImage::class);
    }

    public function flights() {
        return $this->hasMany(Flight::class);
    }

    public function hotels() {
        return $this->hasMany(Hotel::class);
    }


}
