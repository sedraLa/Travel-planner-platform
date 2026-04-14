<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripReservation extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'trip_id',
        'trip_package_id',
        'trip_schedule_id',
        'people_count',
        'total_price',
        'status',
        'guide_earning',
        'guide_id',
        'guide_paid_at',
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

    public function trip()
{
    return $this->belongsTo(Trip::class);
}

public function package()
{
    return $this->belongsTo(TripPackage::class, 'trip_package_id');
}

public function schedule()
{
    return $this->belongsTo(TripSchedule::class, 'trip_schedule_id');
}
}
