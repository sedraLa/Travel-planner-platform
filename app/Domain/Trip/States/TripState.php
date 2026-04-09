<?php

namespace App\Domain\Trip\States;

abstract class TripState
{
    abstract public function name(): string;

    public function canTransitionTo(string $nextStatus): bool
    {
        return in_array($nextStatus, $this->allowedTransitions(), true);
    }

    /**
     * @return string[]
     */
    abstract protected function allowedTransitions(): array;
}

