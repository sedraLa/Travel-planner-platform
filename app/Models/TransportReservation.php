<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportReservation extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'pickup_location',
        'dropoff_location',
        'pickup_datetime',
        'dropoff_datetime',
        'passengers',
        'total_price',
        'driver_earning',
        'status',
        'transport_vehicle_id',
        'driver_id',
        'driver_status',
        'ranked_driver_ids',

    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'pickup_datetime' => 'datetime',
        'dropoff_datetime' => 'datetime',
        'ranked_driver_ids' => 'array',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }


    public function vehicle() {
        return $this->belongsTo(TransportVehicle::class,'transport_vehicle_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
      public function driver()
    {
        return $this->belongsTo(Driver::class);
    }


    public function bookingRequests()
    {
        return $this->hasMany(BookingRequest::class, 'reservation_id');
    }
}
