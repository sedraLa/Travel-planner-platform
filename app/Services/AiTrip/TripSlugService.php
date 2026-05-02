<?php

namespace App\Services\AiTrip;

use App\Models\Trip;

class TripSlugService
{
    public function nextUniqueSlug(string $slugBase): string
    {
        $slug = $slugBase ?: 'ai-trip';
        $counter = 1;

        while (Trip::query()->where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
