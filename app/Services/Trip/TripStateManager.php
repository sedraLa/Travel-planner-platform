<?php

namespace App\Services\Trip;

use App\Domain\Trip\Factory\TripStateFactory;
use App\Models\Trip;
use LogicException;

class TripStateManager
{
    public function transition(Trip $trip, string $nextStatus): Trip
    {
        //same status
        if ($trip->status === $nextStatus) {
            return $trip->refresh();
        }

        $current = TripStateFactory::make($trip->status);

        if (! $current->canTransitionTo($nextStatus)) {
            throw new LogicException("Cannot transition trip from {$trip->status} to {$nextStatus}.");
        }

        $trip->update(['status' => $nextStatus]);

        return $trip->refresh();
    }
}
