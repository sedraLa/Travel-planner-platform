<?php
namespace App\Domain\TransportReservation\States\PendingDiiverState;

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

