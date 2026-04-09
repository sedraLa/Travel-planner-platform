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
        'description',
        'duration_days',
        'category',
        'max_participants',
        'meeting_point_description',
        'meeting_point_address',
        'is_ai_generated',
        'ai_prompt',
        'status',
        'guide_specialization_ids',
        'requires_tour_leader',
        'driver_vehicle_type',
        'driver_vehicle_capacity',
        'driver_trip_type',
        'driver_road_type',
        'ranked_guide_ids',
        'ranked_driver_ids',
        'assigned_guide_id',
        'assigned_driver_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'guide_specialization_ids' => 'array',
        'requires_tour_leader' => 'boolean',
        'ranked_guide_ids' => 'array',
        'ranked_driver_ids' => 'array',
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
   

    public function guideRequests()
    {
        return $this->hasMany(GuideRequest::class);
    }

    public function driverRequests()
    {
        return $this->hasMany(DriverRequest::class);
    }

    public function assignedGuide()
    {
        return $this->belongsTo(Guide::class, 'assigned_guide_id');
    }

    public function assignedDriver()
    {
        return $this->belongsTo(Driver::class, 'assigned_driver_id');
    }

    public function guides()
    {
        return $this->belongsToMany(Guide::class, 'guide_assignments')
            ->withPivot('status')
            ->withTimestamps();
    }

    // Clear relation name for the primary destination.
    public function primaryDestination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    // Clear relation name for all itinerary destinations (including primary).
    public function itineraryDestinations()
    {
        return $this->belongsToMany(Destination::class, 'trip_destinations')
            ->withPivot('sort_order')
            ->orderBy('trip_destinations.sort_order');
    }

    // Backward-compatible alias.
    public function destination()
    {
        return $this->primaryDestination();
    }

    // Backward-compatible alias.
    public function destinations()
    {
        return $this->itineraryDestinations();
    }

}
