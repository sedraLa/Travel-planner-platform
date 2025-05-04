<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory ,SoftDeletes;

    protected $fillable = [
        'reservation_id',
        'user_id',
        'amount',
        'status',
        'transaction_id',
        'payment_date',
    ];


    protected $hidden = [
        'created_at', 
        'updated_at'
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id', 'reservation_id');
    }

    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }




}
