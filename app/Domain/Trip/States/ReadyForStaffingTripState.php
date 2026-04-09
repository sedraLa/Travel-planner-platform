<?php

namespace App\Domain\Trip\States;

class ReadyForStaffingTripState extends TripState
{
    public function name(): string
    {
        return 'ready_for_staffing';
    }

    protected function allowedTransitions(): array
    {
        return ['staffing_in_progress'];
    }
}
