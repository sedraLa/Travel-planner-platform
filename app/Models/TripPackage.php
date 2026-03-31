<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripPackage extends Model
{
    use HasFactory;

      protected $fillable = ['trip_id', 'name', 'price'];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function includes()
    {
        return $this->hasMany(TripInclude::class);
    }

    public function excludes()
    {
        return $this->hasMany(TripExclude::class);
    }

    public function highlights()
    {
        return $this->hasMany(TripHighlight::class);
    }

    public function infos()
    {
        return $this->hasMany(TripPackageInfo::class);
    }


    public function packageHotels()
   {
    return $this->hasMany(TripPackageHotel::class);
   }
   
    public function hotels()
    {
        return $this->belongsToMany(Hotel::class, 'trip_package_hotels')
            ->withPivot(['room_type', 'amenities', 'meal_plan'])
            ->withTimestamps();
    }
}
