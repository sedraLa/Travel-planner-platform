<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Guide extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'languages',
        'years_of_experience',
        'certificate_image',
        'status',
        'earnings_balance',
        'personal_image',
        'age',
        'address',
        'date_of_hire',
        'total_trips_count',
        'last_trip_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


   
    public function availabilities()
    {
        return $this->hasMany(GuideAvailability::class);
    }


    public function guideRequests()
    {
        return $this->hasMany(GuideRequest::class);
    }

    public function assignments()
    {
        return $this->hasMany(GuideAssignment::class);
    }
    
    public function trips()
     {
    return $this->belongsToMany(Trip::class, 'guide_assignments')
        ->withPivot('status')
        ->withTimestamps();
     }
    public function reservations()
    {
        return $this->hasMany(TripReservation::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

}
