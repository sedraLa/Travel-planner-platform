<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportVehicle extends Model
{
    use HasFactory;
    protected $fillable = [
    'car_model',
    'plate_number',
    'max_passengers',
    'base_price',
    'price_per_km',
    'category',
    'image',
    'type',
];
        
    protected $hidden = [
        'created_at', 
        'updated_at'
    ];

    public function reservations()
    {
        return $this->hasMany(TransportReservation::class);
    }
    

    public function assignments()
    {
    return $this->hasMany(Assignment::class); 
    }
   

    public function tripTransports()
    {
        return $this->hasMany(TripTransport::class);
    }

}
