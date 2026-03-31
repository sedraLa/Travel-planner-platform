<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripHighlight extends Model
{
    use HasFactory;
     protected $fillable = [
    'trip_package_id', 
    'title', 
    'description',
     ];
     
     public function tripPackage()
    {
        return $this->belongsTo(TripPackage::class);
    }
}
