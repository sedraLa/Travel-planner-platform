<?php

namespace App\Domain\Trip\States;

class StaffingInProgressTripState extends TripState
{
    public function name(): string
    {
        return 'staffing_in_progress';
    }

    protected function allowedTransitions(): array
    {
        return ['staffed'];
    }
}
