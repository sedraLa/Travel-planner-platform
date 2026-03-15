<?php

namespace App\Domain\DriverApplication\States;

use App\Models\Driver;

interface DriverApplicationState
{
    public function apply(Driver $driver): void;

    public function status(): string;

    public function emailMessage(): string;

    public function shouldDeleteDriver(): bool;
}
