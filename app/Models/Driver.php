<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'age',
        'address',
        'license_image',
        'experience',
        'license_category',
        'personal_image',
        'date_of_hire',
        'status',
        'assignment_id',
        'last_trip_at',
        'total_trips_count',
        'earnings_balance',
    ];

    
    public function vehicle()
    {
        return $this->hasOne(TransportVehicle::class);
    }

   
    public function reservations()
    {
        return $this->hasMany(TransportReservation::class);
    }


     public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function assignment()
    {
     return $this->belongsTo(Assignment::class); 
    
    }


    public function bookingRequests()
    {
    return $this->hasMany(BookingRequest::class);
    }

}