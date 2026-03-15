<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Guide;

class Specialization extends Model
{
    use HasFactory;


     protected $fillable = ['name'];

      public function guides()
    {

        return $this->belongsToMany(Guide::class, 'guide_specialization', 'specialization_id', 'guide_id');
    }

}
