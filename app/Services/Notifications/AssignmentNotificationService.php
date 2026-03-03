<?php

namespace App\Services\Notifications;

use App\Models\Assignment;
use App\Models\Driver;
use App\Notifications\DriverAssignmentNotification;

class AssignmentNotificationService
{
    public function notifyDriverAssigned(Driver $driver, Assignment $assignment): void
    {
        if (! $driver->user) {
            return;
        }

        $driver->user->notify(new DriverAssignmentNotification(
            assignment: $assignment,
            message: 'You have been assigned a vehicle and a shift',
        ));
    }

    public function notifyDriverShiftOrVehicleChanged(Driver $driver, Assignment $assignment): void
    {
        if (! $driver->user) {
            return;
        }

        $driver->user->notify(new DriverAssignmentNotification(
            assignment: $assignment,
            message: 'Your assigned vehicle or shift has been changed',
        ));
    }

    public function notifyDriverUnassigned(Driver $driver, Assignment $assignment, string $message): void
    {
        if (! $driver->user) {
            return;
        }

        $driver->user->notify(new DriverAssignmentNotification(
            assignment: $assignment,
            message: 'Your current vehicle and shift have been removed. You will be assigned a new vehicle and shift shortly.',
        ));
    }
}