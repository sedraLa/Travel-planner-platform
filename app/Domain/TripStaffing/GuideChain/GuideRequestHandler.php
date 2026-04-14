<?php

namespace App\Domain\TripStaffing\GuideChain;

use App\Models\Trip;

abstract class GuideRequestHandler
{
    protected ?GuideRequestHandler $next = null;

    public function setNext(GuideRequestHandler $next): GuideRequestHandler
    {
        $this->next = $next;

        return $next;
    }

    public function handle(Trip $trip, array $rankedGuideIds, int $index = 0): void
    {
        if (! $this->process($trip, $rankedGuideIds, $index) && $this->next) {
            $this->next->handle($trip, $rankedGuideIds, $index + 1);
        }
    }

    abstract protected function process(Trip $trip, array $rankedGuideIds, int $index): bool;
}
