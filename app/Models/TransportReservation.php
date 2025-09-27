<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportReservation extends Model
{
    use HasFactory;
    protected $fillable = [
        'transport_id',
        'user_id',
        'pickup_location',
        'dropoff_location',
        'pickup_datetime',
        'dropoff_datetime',
        'passengers',
        'total_price',
        'status',
        'transport_vehicle_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function transport() {
        return $this->belongsTo(Transport::class);
    }

    public function vehicle() {
        return $this->belongsTo(TransportVehicle::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
