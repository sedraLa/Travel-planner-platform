<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'type',
        'price',
        'max_passengers',
        'description',
        'image'
    ];

    protected $hidden = [
        'created_at', 
        'updated_at'
    ];

    public function reservations() {
        return $this->hasMany(TransportReservation::class);
    }

    public function vehicles() {
        return $this->hasMany(TransportVehicle::class);
    }
    


}
