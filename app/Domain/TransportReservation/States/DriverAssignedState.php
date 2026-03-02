<?php

namespace App\Domain\TransportReservation\States;

class DriverAssignedState extends ReservationState
{
    public function name(): string
    {
        return 'driver_assigned';
    }

    protected function allowedTransitions(): array
    {
        return ['confirmed', 'cancelled'];
    }
}
