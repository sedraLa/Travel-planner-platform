<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Hotel extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'city',
        'country',
        'description',
        'address',
        'global_rating',
        'price_per_night',
        'total_rooms',
        'destination_id',
        'stars',
        'amenities',
        'pets_allowed',
        'check_in_time',
        'check_out_time',
        'policies',
        'phone_number',
        'email',
        'website',
        'nearby_landmarks',
    ];


    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
    'check_in_time' => 'datetime:H:i',
    'check_out_time' => 'datetime:H:i',
    'amenities' => 'array',
    'pets_allowed' => 'boolean',
];


    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public function images()
    {
        return $this->hasMany(HotelImage::class, 'hotel_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'hotel_id');
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }
}
