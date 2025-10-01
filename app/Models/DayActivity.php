<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayActivity extends Model
{
    use HasFactory;
    protected $fillable = [
        'trip_day_id',
        'activity_id',
        'custom_activity'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function tripDay() {
        return $this->belongsTo(TripDay::class);
    }

    public function activity() {
        return $this->belongsTo(Activity::class);
    }

    
}
