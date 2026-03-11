<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Specialization extends Model
{
    use HasFactory;


     protected $fillable = ['name'];

        public function guides()
    {
        return $this->belongsToMany(User::class);
    }

}
