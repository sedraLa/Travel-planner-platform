<?php

namespace App\Domain\Trip\States;

class StaffedTripState extends TripState
{
    public function name(): string
    {
        return 'staffed';
    }

    protected function allowedTransitions(): array
    {
        return ['published'];
    }
}
