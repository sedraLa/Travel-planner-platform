<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Favorite;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
        'ranked_guide_ids',
        'assigned_guide_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'ranked_guide_ids' => 'array',
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
     
     

    public function assignments()
   {
    return $this->hasMany(GuideAssignment::class);
   }
   

    public function guideRequests()
    {
        return $this->hasMany(GuideRequest::class);
    }

    public function assignedGuide()
    {
        return $this->belongsTo(Guide::class, 'assigned_guide_id');
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


    public function favorites():MorphMany
     {
    return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function reservations()
    {
        return $this->hasMany(TripReservation::class);
    }

    public function hasOpenBookingWindow(): bool
    {
        $today = now()->toDateString();

        return $this->schedules()
            ->whereDate('booking_deadline', '>=', $today)
            ->where('status', 'available')
            ->where('available_seats', '>', 0)
            ->exists();
    }

}
