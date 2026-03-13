<?php

namespace App\Domain\DriverApplication\States;

use App\Models\Driver;

class RejectedDriverApplicationState extends AbstractDriverApplicationState
{
    public function apply(Driver $driver): void
    {
        $driver->update([
            'status' => $this->status(),
        ]);
    }

    public function status(): string
    {
        return 'rejected';
    }

    public function emailMessage(): string
    {
        return 'Sorry, your account has been rejected after review of the information.';
    }

    public function shouldDeleteDriver(): bool
    {
        return true;
    }
}
