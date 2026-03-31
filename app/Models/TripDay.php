<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripDay extends Model
{
    use HasFactory;

    protected $fillable = [
         'trip_id',
          'day_number',
          'title',
           'description',
            'highlights', 
            'hotel_id',
    ];


    protected $casts = [
        'highlights' => 'array'
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function activities()
    {
        return $this->hasMany(DayActivity::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
  

}


