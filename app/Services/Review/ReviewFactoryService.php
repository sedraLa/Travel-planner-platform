<?php

namespace App\Services\Review;

class ReviewFactoryService
{
    public function create(Model $model, array $data)
    {
        return $model->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $data['rating'],
            'review' => $data['review'] ?? null,
        ]);
    }
}