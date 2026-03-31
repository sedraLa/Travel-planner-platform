<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripExclude extends Model
{
    use HasFactory;
     protected $fillable = [
    'trip_package_id', 
    'content',
     ];
     
      public function tripPackage()
    {
        return $this->belongsTo(TripPackage::class);
    }
}
