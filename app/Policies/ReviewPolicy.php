<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Review;

class ReviewPolicy
{
    /**
     * Update review
     */
    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }

    /**
     * Delete review
     */
public function delete(User $user, Review $review): bool
{
    return $user->role === 'admin';
}
}