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
        'check_in_date',
        'check_out_date',
        'rooms_count',
        'guest_count',
        'total_price',
        'reservation_status',
    ];

    protected $hidden = [
        'created_at', 
        'updated_at'
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');

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
