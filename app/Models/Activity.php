<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'is_active',

        // الإضافات الجديدة
        'start_time',
        'end_time',
        'start_date',
        'end_date',
        'availability',
        'guide_name',
        'guide_language',
        'contact_number',
        'requirements',
        'difficulty_level',
        'amenities',
        'address',
        'requires_booking',
        'family_friendly',
        'pets_allowed',
        'highlights',
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
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
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
}
