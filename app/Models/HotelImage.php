<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelImage extends Model
{
    
    use HasFactory;
/////ddddd
    protected $fillable = [
        'image_url',
        'is_primary',
        'hotel_id',
    ];

    protected $hidden = [
        'created_at', 
        'updated_at'
    ];
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }
}
