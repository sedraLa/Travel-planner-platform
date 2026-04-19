<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'name',
        'price_per_night',
        'capacity',
        'quantity',
        'description',
        'amenities',
        'is_refundable',
    ];

    protected $casts = [
        'amenities' => 'array',
        'is_refundable' => 'boolean',
        'price_per_night' => 'float',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(RoomTypeImage::class, 'room_type_id');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(RoomTypeImage::class, 'room_type_id')->where('is_primary', true);
    }
}
