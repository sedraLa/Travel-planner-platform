<?php

namespace App\Domain\Trip\States;

class DraftTripState extends TripState
{
    public function name(): string
    {
        return 'draft';
    }

    protected function allowedTransitions(): array
    {
        return ['ready_for_assignment'];
    }
}
