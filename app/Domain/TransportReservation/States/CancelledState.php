<?php

namespace App\Domain\TransportReservation\States;

class CancelledState extends ReservationState
{
    public function name(): string
    {
        return 'cancelled';
    }

    protected function allowedTransitions(): array
    {
        return [];
    }
}
