<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Reservation extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'user_id',
        'hotel_id',
        'room_type_id',
        'check_in_date',
        'check_out_date',
        'check_in',
        'check_out',
        'rooms_count',
        'guest_count',
        'guests',
        'total_price',
        'reservation_status',
        'hotel_review_notification_sent',
    ];

    protected $hidden = [
        'created_at', 
        'updated_at'
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'check_in' => 'date',
        'check_out' => 'date',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');

    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payment()
{
    return $this->hasOne(Payment::class, 'reservation_id');
}

}
