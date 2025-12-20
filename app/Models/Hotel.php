<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Favorite;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
        'stars',
        'pets_allowed',
        'check_in_time',
        'check_out_time',
        'policies',
        'phone_number',
        'email',
        'website',
        'amenities', // ممكن تخزنيها كـ JSON
        'latitude',
        'longitude',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
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
