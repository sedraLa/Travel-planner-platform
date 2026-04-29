<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ActivityReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_id',
        'activity_date',
        'participants_count',
        'total_price',
        'status',
        'activity_review_notification_sent',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
