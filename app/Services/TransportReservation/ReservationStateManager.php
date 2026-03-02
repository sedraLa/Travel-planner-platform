<?php

namespace App\Services\TransportReservation;

use App\Domain\TransportReservation\Factory\ReservationStateFactory;
use App\Models\TransportReservation;
use LogicException;

class ReservationStateManager
{
    public function transition(TransportReservation $reservation, string $nextStatus): TransportReservation
    {
        $current = ReservationStateFactory::make($reservation->status);

        if (!$current->canTransitionTo($nextStatus)) {
            throw new LogicException("Cannot transition reservation from {$reservation->status} to {$nextStatus}.");
        }

        $reservation->update(['status' => $nextStatus]);

        return $reservation->refresh();
    }
}
