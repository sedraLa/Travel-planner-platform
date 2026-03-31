<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;
    protected $fillable = [
        'destination_id',
        'name',
        'slug',
        'duration_days',
        'category',
        'max_participants',
        'meeting_point_description',
        'meeting_point_address',
        'is_ai_generated',
        'ai_prompt',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

   public function packages()
    {
        return $this->hasMany(TripPackage::class);
    }
    public function images()
    {
        return $this->hasMany(TripImage::class);
    }
     public function schedules()
    {
        return $this->hasMany(TripSchedule::class);
    }

   public function days()
    {
        return $this->hasMany(TripDay::class);
    }
      public function transports()
    {
        return $this->hasMany(TripTransport::class);
    }
     

    public function assignments()
   {
    return $this->hasMany(GuideAssignment::class);
   }
   
    public function guides()
    {
        return $this->belongsToMany(Guide::class, 'guide_assignments')
            ->withPivot('status')
            ->withTimestamps();
    }


  public function destination()
  {
    return $this->belongsTo(Destination::class);
  }

}
