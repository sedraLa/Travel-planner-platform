<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripSchedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'trip_id',
        'start_date',
        'end_date',
        'booking_deadline',
        'available_seats',
        'price_modifier',
        'status',
    ];
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
