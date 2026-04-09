<?php

namespace App\Jobs\TripStaffing;

use App\Domain\TripStaffing\DriverChain\SendToNextTripDriverHandler;
use App\Models\Trip;
use App\Services\TripStaffing\TripStaffingCoordinator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessNextDriverInChainJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $tripId, public array $rankedDriverIds, public int $index)
    {
    }

    public function handle(TripStaffingCoordinator $coordinator): void
    {
        $trip = Trip::find($this->tripId);

        if (! $trip || $trip->status !== 'staffing_in_progress' || $trip->assigned_driver_id) {
            return;
        }

        if (! isset($this->rankedDriverIds[$this->index])) {
            $coordinator->failTripStaffing($trip, 'Driver assignment timed out for all candidates.');

            return;
        }

        $handler = new SendToNextTripDriverHandler();
        $handler->handle($trip, $this->rankedDriverIds, $this->index);
    }
}
