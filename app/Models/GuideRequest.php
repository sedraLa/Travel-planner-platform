<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuideRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'guide_id',
        'chain_index',
        'status',
        'expires_at',
        'responded_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function guide()
    {
        return $this->belongsTo(Guide::class);
    }
}
