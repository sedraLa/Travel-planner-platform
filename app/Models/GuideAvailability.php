<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuideAvailability extends Model
{
    use HasFactory;
    protected $fillable = [
    'guide_id',  
    'date',  
    'start_time',
    'end_time'
    ];
    
    public function guide()
    {
        return $this->belongsTo(Guide::class);
    }

}
