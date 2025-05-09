<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\SoftDeletes;


=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

>>>>>>> 19be65ce52db7c6789256e8d1cf27513c4120e22

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
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }




}
