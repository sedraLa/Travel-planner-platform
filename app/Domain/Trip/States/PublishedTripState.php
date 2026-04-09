<?php

namespace App\Domain\Trip\States;

class PublishedTripState extends TripState
{
    public function name(): string
    {
        return 'published';
    }

    protected function allowedTransitions(): array
    {
        return [];
    }
}
