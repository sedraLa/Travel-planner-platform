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

    public function images() {
        return $this->hasMany(DestinationImage::class);
    }


}
