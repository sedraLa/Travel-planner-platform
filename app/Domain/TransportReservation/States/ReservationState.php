<?php

namespace App\Domain\TransportReservation\States;

abstract class ReservationState
{
    //get status name
    abstract public function name(): string;

    //concrete method in every state to check if it can move to the next state
    public function canTransitionTo(string $nextStatus): bool
    {
        return in_array($nextStatus, $this->allowedTransitions(), true);
    }

    /**
     * @return string[]
     */
    //get array of allowed transitions from this state
    abstract protected function allowedTransitions(): array;
}
