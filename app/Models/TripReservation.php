<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripReservation extends Model
{
    use HasFactory;
     protected $fillable = [
    'user_id',  
    'day_activity_id',
    'guide_id',
    'status',
    'guide_earning',
       ];

        public function guide()
    {
        return $this->belongsTo(Guide::class);
    }

    public function dayActivity()
    {
        return $this->belongsTo(DayActivity::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
