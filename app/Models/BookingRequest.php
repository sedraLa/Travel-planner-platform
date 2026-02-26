<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'reservation_id',
        'driver_id',
        'status',
        'expires_at',
    ];


     public function reservation()
    {
        return $this->belongsTo(TransportReservation::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
