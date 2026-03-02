<?php

namespace App\Domain\TransportReservation\Factory;

use App\Domain\TransportReservation\States\CancelledState;
use App\Domain\TransportReservation\States\ConfirmedState;
use App\Domain\TransportReservation\States\DriverAssignedState;
use App\Domain\TransportReservation\States\PendingDriverState;
use App\Domain\TransportReservation\States\PendingPaymentState;
use App\Domain\TransportReservation\States\ReservationState;
use InvalidArgumentException;  //throw exception if status id unknown

class ReservationStateFactory
{
    //make object of the status
    public static function make(string $status): ReservationState
    {
        return match ($status) {
            'pending_payment' => new PendingPaymentState(),
            'pending_driver' => new PendingDriverState(),
            'driver_assigned' => new DriverAssignedState(),
            'confirmed' => new ConfirmedState(),
            'cancelled' => new CancelledState(),
            default => throw new InvalidArgumentException("Unsupported reservation status [{$status}]."),
        };
    }
}
