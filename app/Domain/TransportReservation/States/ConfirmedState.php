<?php

namespace App\Domain\TransportReservation\States;

class ConfirmedState extends ReservationState
{
    public function name(): string
    {
        return 'confirmed';
    }

    protected function allowedTransitions(): array
    {
        return [];
    }
}
