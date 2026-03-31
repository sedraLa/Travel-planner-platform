<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuideAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
    'trip_id',
    'guide_id',
    'status',
     ];


      public function guide()
    {
        return $this->belongsTo(Guide::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
