<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Favorite;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Carbon\Carbon;

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

    //packages
   public function packages()
    {
        return $this->hasMany(TripPackage::class);
    }
    
    //images
    public function images()
    {
        return $this->hasMany(TripImage::class);
    }

    //schedules
     public function schedules()
    {
        return $this->hasMany(TripSchedule::class);
    }

    //days
   public function days()
    {
        return $this->hasMany(TripDay::class);
    }


    //assignments
    public function assignments()
   {
    return $this->hasMany(GuideAssignment::class);
   }


   //guide requests
    public function guideRequests()
    {
        return $this->hasMany(GuideRequest::class);
    }

    //assigned guide
    public function assignedGuide()
    {
        return $this->belongsTo(Guide::class, 'assigned_guide_id');
    }

    //guides
    public function guides()
    {
        return $this->belongsToMany(Guide::class, 'guide_assignments')
            ->withPivot('status')
            ->withTimestamps();
    }

    // primary destination.
    public function primaryDestination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    //  itinerary destinations.
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

    //fav
    public function favorites():MorphMany
     {
    return $this->morphMany(Favorite::class, 'favoritable');
    }

    //reviews
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    //reservations
    public function reservations()
    {
        return $this->hasMany(TripReservation::class);
    }

    //accessories
    public function hasOpenBookingWindow(): bool
    {
        $today = now()->toDateString();
        return $this->schedules()
            ->whereDate('booking_deadline', '>=', $today)
            ->where('status', 'available')
            ->where('available_seats', '>', 0)
            ->exists();
    }


    public function isBookingClosed(): bool
    {
    $today = Carbon::today();
    return $this->schedules->isEmpty() || $this->schedules->every(function ($schedule) use ($today) {
        if (! $schedule->booking_deadline) {
            return true;
        }

        return Carbon::parse($schedule->booking_deadline)->lt($today)
            || $schedule->status !== 'available'
            || $schedule->available_seats <= 0;
    });
}

}
