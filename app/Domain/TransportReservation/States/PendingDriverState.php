<?php
namespace App\Domain\TransportReservation\States;

class PendingDriverState extends ReservationState
{
    public function name(): string {
        return 'pending_driver';
    }

    protected function allowedTransitions(): array
    {
        return ['driver_assigned', 'cancelled'];
    }

}

