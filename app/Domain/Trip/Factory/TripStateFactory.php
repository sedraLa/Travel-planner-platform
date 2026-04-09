<?php

namespace App\Domain\Trip\Factory;

use App\Domain\Trip\States\DraftTripState;
use App\Domain\Trip\States\PublishedTripState;
use App\Domain\Trip\States\ReadyForAssignmentTripState;
use App\Domain\Trip\States\ReadyForStaffingTripState;
use App\Domain\Trip\States\StaffedTripState;
use App\Domain\Trip\States\StaffingInProgressTripState;
use App\Domain\Trip\States\TripState;
use InvalidArgumentException;

class TripStateFactory
{
    public static function make(string $status): TripState
    {
        return match ($status) {
            'draft' => new DraftTripState(),
            'ready_for_assignment' => new ReadyForAssignmentTripState(),
            'ready_for_staffing' => new ReadyForStaffingTripState(),
            'staffing_in_progress' => new StaffingInProgressTripState(),
            'staffed' => new StaffedTripState(),
            'published' => new PublishedTripState(),
            default => throw new InvalidArgumentException("Unsupported trip status [{$status}]."),
        };
    }
}
