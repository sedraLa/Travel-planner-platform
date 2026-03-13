<?php

namespace App\Domain\DriverApplication\States;

use App\Models\Driver;
use Carbon\Carbon;

class ApprovedDriverApplicationState extends AbstractDriverApplicationState
{
    public function apply(Driver $driver): void
    {
        $driver->update([
            'status' => $this->status(),
            'date_of_hire' => Carbon::now(),
        ]);
    }

    public function status(): string
    {
        return 'approved';
    }

    public function emailMessage(): string
    {
        return 'Your account has been approved! You can now log in to the system.';
    }
}
