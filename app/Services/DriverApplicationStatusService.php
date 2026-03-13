<?php

namespace App\Services;

use App\Domain\DriverApplication\Factory\DriverApplicationStateFactory;
use App\Mail\DriverStatusMail;
use App\Models\Driver;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class DriverApplicationStatusService
{
    public function __construct(
        private readonly DriverApplicationStateFactory $stateFactory
    ) {
    }

    public function updateStatus(Driver $driver, string $status): void
    {
        $state = $this->stateFactory->make($status);
        $state->apply($driver);

        Mail::to($driver->user->email)
            ->send(new DriverStatusMail($driver->user->name, $state->status(), $state->emailMessage()));

        if ($state->shouldDeleteDriver()) {
            $this->deleteDriverAssets($driver);
            $driver->user()->delete();
            $driver->delete();
        }
    }

    private function deleteDriverAssets(Driver $driver): void
    {
        foreach (['license_image', 'personal_image'] as $field) {
            $path = $driver->{$field};

            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
