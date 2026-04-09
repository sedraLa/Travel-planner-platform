<?php

namespace App\Domain\TripStaffing\DriverChain;

use App\Models\Trip;

abstract class TripDriverRequestHandler
{
    protected ?TripDriverRequestHandler $next = null;

    public function setNext(TripDriverRequestHandler $next): TripDriverRequestHandler
    {
        $this->next = $next;

        return $next;
    }

    public function handle(Trip $trip, array $rankedDriverIds, int $index = 0): void
    {
        if (! $this->process($trip, $rankedDriverIds, $index) && $this->next) {
            $this->next->handle($trip, $rankedDriverIds, $index + 1);
        }
    }

    abstract protected function process(Trip $trip, array $rankedDriverIds, int $index): bool;
}
