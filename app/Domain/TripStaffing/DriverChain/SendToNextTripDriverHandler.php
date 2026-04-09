<?php

namespace App\Domain\TripStaffing\DriverChain;

use App\Jobs\TripStaffing\SendDriverRequestJob;
use App\Models\Trip;

class SendToNextTripDriverHandler extends TripDriverRequestHandler
{
    protected function process(Trip $trip, array $rankedDriverIds, int $index): bool
    {
        if (! isset($rankedDriverIds[$index])) {
            return false;
        }

        SendDriverRequestJob::dispatchSync($trip->id, $rankedDriverIds[$index], $rankedDriverIds, $index);

        return true;
    }
}
