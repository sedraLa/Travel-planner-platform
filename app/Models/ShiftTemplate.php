<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ShiftTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'days_of_week',
    ];

    protected $casts = [
    'days_of_week' => 'array',
    ];


    protected $hidden = [
        'created_at',
        'updated_at'
    ];
  

     public function assignments() 
     { 
      return $this->hasMany(Assignment::class); 
     }


}