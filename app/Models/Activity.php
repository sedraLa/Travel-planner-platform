<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Favorite;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'destination_id',
        'description',
        'duration',
        'duration_unit',
        'price',
        'category',
        'start_date',
        'end_date',
        'availability',
        'contact_number',
        'contact_email',
        'requirements',
        'difficulty_level',
        'amenities',
        'address',
        'requires_booking',
        'family_friendly',
        'pets_allowed',
      
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_booking' => 'boolean',
        'pets_allowed' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'amenities' => 'array',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function dayActivities()
    {
        return $this->hasMany(DayActivity::class);
    }

    
    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }


    public function reservations()
   {
    return $this->hasMany(ActivityReservation::class);
   }

   public function highlights()
{
    return $this->hasMany(ActivityHighlight::class);
}
}
