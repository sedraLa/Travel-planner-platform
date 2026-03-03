<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

   protected $fillable = [
        'transport_vehicle_id',
        'shift_template_id',
        'drivre_is',
    ];

    public function vehicle()
    {
        return $this->belongsTo(TransportVehicle::class);
    }

    public function shiftTemplate()
    {
        return $this->belongsTo(ShiftTemplate::class);
    }


    public function driver()
    {
     return $this->belongsTo(Driver::class);
    }







}
