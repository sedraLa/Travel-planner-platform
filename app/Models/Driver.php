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
        'date_of_hire',
        'status',
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


}