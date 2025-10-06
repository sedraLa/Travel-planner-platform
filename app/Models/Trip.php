<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'destination_id',
        'hotel_id',
        'destination_name',
        'hotel_name',
        'name',
        'description',
        'travelers_number',
        'budget',
        'start_date',
        'end_date',
        'flight_number',
        'airline',
        'departure_airport',
        'arrival_airport',
        'departure_time',
        'arrival_time'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function days() {
        return $this->hasMany(TripDay::class);
    }


}
