<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripTransport extends Model
{
    use HasFactory;
    protected $fillable = [
        'trip_id',
        'transport_vehicle_id',
        'driver_id',
        'transport_type',
        'departure_time',
        'return_time',
        'notes',
    ];
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    
    public function vehicle()
    {
        return $this->belongsTo(TransportVehicle::class, 'transport_vehicle_id');
    }

    
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

}
