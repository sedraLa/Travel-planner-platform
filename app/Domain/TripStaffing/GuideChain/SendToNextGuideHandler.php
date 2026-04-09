<?php

namespace App\Domain\TripStaffing\GuideChain;

use App\Jobs\TripStaffing\SendGuideRequestJob;
use App\Models\Trip;

class SendToNextGuideHandler extends GuideRequestHandler
{
    protected function process(Trip $trip, array $rankedGuideIds, int $index): bool
    {
        if (! isset($rankedGuideIds[$index])) {
            return false;
        }

        SendGuideRequestJob::dispatchSync($trip->id, $rankedGuideIds[$index], $rankedGuideIds, $index);

        return true;
    }
}
