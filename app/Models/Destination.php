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
        'iata_code',
       // 'weather_info',
        'timezone',
        'language',
        'currency',
        'nearest_airport',
        'best_time_to_visit',
        'emergency_numbers',
        'local_tip',
        
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function images() {
        return $this->hasMany(DestinationImage::class);
    }

    public function hotels() {
        return $this->hasMany(Hotel::class);
    }

    public function activities() {
        return $this->hasMany(Activity::class);
    }

    public function highlights()
    {
    return $this->hasMany(Highlight::class);
    }
}
