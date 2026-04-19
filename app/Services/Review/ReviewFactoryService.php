<?php

namespace App\Services\Review;
use Illuminate\Database\Eloquent\Model;

class ReviewFactoryService
{
    public function create(Model $model, array $data)
    {
        return $model->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $data['rating'],
            'review' => $data['review'] ?? null,
            'reservation_id' => $data['reservation_id'],
        ]);
    }
}