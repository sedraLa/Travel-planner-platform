<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportVehicle extends Model
{
    use HasFactory;
    protected $fillable = [
        'transport_id',
        'car_model',
        'plate_number',
        'driver_name',
        'driver_contact'];
        
    protected $hidden = [
        'created_at', 
        'updated_at'
    ];

    public function reservations()
    {
        return $this->hasMany(TransportReservation::class);
    }

    public function transport() {
        return $this->belongsTo(Transport::class);
    }
    
}
