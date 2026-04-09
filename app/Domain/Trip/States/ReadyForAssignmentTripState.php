<?php

namespace App\Domain\Trip\States;

class ReadyForAssignmentTripState extends TripState
{
    public function name(): string
    {
        return 'ready_for_assignment';
    }

    protected function allowedTransitions(): array
    {
        return ['ready_for_staffing'];
    }
}
