<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ShiftTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'days_of_week',
    ];

    protected $casts = [
    'days_of_week' => 'array',
    ];


    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function getStartTimeFormattedAttribute()
{
    return \Carbon\Carbon::parse($this->start_time)->format('H:i');
}

public function getEndTimeFormattedAttribute()
{
    return \Carbon\Carbon::parse($this->end_time)->format('H:i');
}

public function scopeSameTimeAndDays($query, $start, $end, $days)
    {
        $days = collect($days)->sort()->values()->toArray();

        return $query->where('start_time', $start)
            ->where('end_time', $end)
            ->get()
            ->filter(function ($template) use ($days) {
                return collect($template->days_of_week)->sort()->values()->toArray() === $days;
            });
    }
  

     public function assignments() 
     { 
      return $this->hasMany(Assignment::class); 
     }


}