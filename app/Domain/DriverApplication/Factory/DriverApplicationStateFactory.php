<?php

namespace App\Domain\DriverApplication\Factory;

use App\Domain\DriverApplication\States\ApprovedDriverApplicationState;
use App\Domain\DriverApplication\States\DriverApplicationState;
use App\Domain\DriverApplication\States\RejectedDriverApplicationState;
use InvalidArgumentException;

class DriverApplicationStateFactory
{
    public function make(string $status): DriverApplicationState
    {
        return match ($status) {
            'approved' => new ApprovedDriverApplicationState(),
            'rejected' => new RejectedDriverApplicationState(),
            default => throw new InvalidArgumentException("Unsupported driver status transition: {$status}"),
        };
    }
}
