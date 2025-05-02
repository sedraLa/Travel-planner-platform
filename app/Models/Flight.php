<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;
    protected $fillable = [
        'available_seats',
        'airline',
        'flight_number',
        'arrival_airport',
        'retrieved_at',
        'destination_id',
        'flight_id',
        'departure_airport',
        'departure_time',
        'arrival_time',
        'booking_url',
        'duration',
        'price',
    ];
    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }
    



}
