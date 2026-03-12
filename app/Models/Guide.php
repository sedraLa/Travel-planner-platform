<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Guide extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'languages',
        'years_of_experience',
        'certificate_image',
        'status',
        'earnings_balance',
        'personal_image',
        'age',
        'address',
        'date_of_hire',
        'total_trips_count',
        'last_trip_at',
        'is_tour_leader',
        

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


     public function specializations()
    {
        return $this->belongsToMany(Specialization::class, 'guide_specialization', 'guide_id', 'specialization_id');
    }

}
