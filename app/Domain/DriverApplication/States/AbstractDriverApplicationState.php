<?php

namespace App\Domain\DriverApplication\States;

abstract class AbstractDriverApplicationState implements DriverApplicationState
{
    public function shouldDeleteDriver(): bool
    {
        return false;
    }
}
