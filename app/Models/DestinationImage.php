<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestinationImage extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'destination_id',
        'image_url',
        'is_primary',
    ];



    public function destination() {
        return $this->belongsTo(Destination::class);
    }
}
