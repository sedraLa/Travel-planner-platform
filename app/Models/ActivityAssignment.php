<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_activity_id',
        'guide_id',
        'status', // assigned / completed / cancelled
    ];

    public function dayActivity()
    {
        return $this->belongsTo(DayActivity::class);
    }

   
}
