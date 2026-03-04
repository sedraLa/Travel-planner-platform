<?php

namespace App\Services\TransportReservation;

use App\Domain\TransportReservation\Factory\ReservationStateFactory;
use App\Models\TransportReservation;
use LogicException;

class ReservationStateManager
{
    /**
     * Transition reservation to next state
     */
    public function transition(TransportReservation $reservation, string $nextStatus): TransportReservation
    {
        $current = ReservationStateFactory::make($reservation->status);

        if (!$current->canTransitionTo($nextStatus)) {
            throw new LogicException("Cannot transition reservation from {$reservation->status} to {$nextStatus}.");
        }

        $reservation->update(['status' => $nextStatus]);

        return $reservation->refresh();
    }

    /**
     * Set initial state for a new reservation
     */
    public function setInitialState(TransportReservation $reservation, string $status): TransportReservation
    {
        $reservation->update(['status' => $status]);
        return $reservation->refresh();
    }

    /**
     * Check if reservation is in driver_assigned state
     */
    public function isDriverAssigned(TransportReservation $reservation): bool
    {
        return $reservation->status === 'driver_assigned';
    }

    /**
     * Check if reservation is cancelled
     */
    public function isCancelled(TransportReservation $reservation): bool
    {
        return $reservation->status === 'cancelled';
    }

    /**
     * Check if reservation can access searching page
     */
    public function canAccessSearching(TransportReservation $reservation): bool
    {
        return in_array($reservation->status, ['pending_driver', 'driver_assigned'], true);
    }

    /**
     * Check if reservation can access assigned page
     */
    public function canAccessAssigned(TransportReservation $reservation): bool
    {
        return $reservation->status === 'driver_assigned';
    }
}