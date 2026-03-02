<?php

namespace App\Domain\TransportReservation\States;

class PendingPaymentState extends ReservationState
{
    public function name(): string
    {
        return 'pending_payment';
    }

    protected function allowedTransitions(): array
    {
        return ['pending_driver', 'cancelled'];
    }
}
