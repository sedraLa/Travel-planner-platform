<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Jobs\TripStaffing\ProcessNextDriverInChainJob;
use App\Models\DriverRequest;
use App\Services\TripStaffing\TripStaffingCoordinator;
use Illuminate\Http\RedirectResponse;

class TripDriverRequestResponseController extends Controller
{
    public function accept(DriverRequest $driverRequest, TripStaffingCoordinator $coordinator): RedirectResponse
    {
        $driver = auth()->user()?->driver;

        abort_unless($driver && $driverRequest->driver_id === $driver->id, 403);

        if (! $coordinator->acceptDriverRequest($driverRequest)) {
            return back()->withErrors('Driver request is no longer pending.');
        }

        return back()->with('success', 'Driver request accepted successfully.');
    }

    public function reject(DriverRequest $driverRequest, TripStaffingCoordinator $coordinator): RedirectResponse
    {
        $driver = auth()->user()?->driver;

        abort_unless($driver && $driverRequest->driver_id === $driver->id, 403);
        abort_unless($driverRequest->status === 'pending', 422, 'Request is no longer pending.');

        $coordinator->rejectDriverRequest($driverRequest);

        $rankedDriverIds = $driverRequest->trip->ranked_driver_ids ?? [];
        $currentIndex = array_search($driver->id, $rankedDriverIds, true);

        ProcessNextDriverInChainJob::dispatchSync(
            $driverRequest->trip_id,
            $rankedDriverIds,
            $currentIndex === false ? ($driverRequest->chain_index + 1) : $currentIndex + 1
        );

        return back()->with('success', 'Driver request rejected.');
    }
}
