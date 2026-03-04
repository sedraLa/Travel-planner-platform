<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingRequest extends Model
{
    protected $table = 'booking_request';
    use HasFactory;
    protected $fillable = [
        'reservation_id',
        'driver_id',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];


    public function reservation()
    {
        return $this->belongsTo(TransportReservation::class, 'reservation_id', 'id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
