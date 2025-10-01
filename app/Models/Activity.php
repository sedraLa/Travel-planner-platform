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
        'is_active'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function destination() {
        return $this->belongsTo(Destination::class);
    }

    public function dayActivities() {
        return $this->hasMany(DayActivity::class);
    }
    

    
}
