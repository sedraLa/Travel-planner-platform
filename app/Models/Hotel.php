<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'address',
        'city',
        'country',
        'global_rating',
        'price_per_night',
        'total_rooms',
        'destination_id',
    ];


    protected $hidden = [
        'created_at',
        'updated_at'
    ];



    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }


    public function images()
    {
        return $this->hasMany(HotelImage::class, 'hotel_id');
    }


}
