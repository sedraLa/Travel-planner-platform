<?php

namespace App\Jobs\TripStaffing;

use App\Domain\TripStaffing\DriverChain\SendToNextTripDriverHandler;
use App\Models\Trip;
use App\Services\TripStaffing\DriverRankingService;
use App\Services\TripStaffing\TripStaffingCoordinator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTripDriverMatchingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $tripId)
    {
    }

    public function handle(DriverRankingService $rankingService, TripStaffingCoordinator $coordinator): void
    {
        $trip = Trip::find($this->tripId);

        if (! $trip || $trip->status !== 'staffing_in_progress' || $trip->assigned_driver_id) {
            return;
        }

        $rankedDriverIds = $rankingService->rankedDriverIdsForTrip($trip);
        $trip->update(['ranked_driver_ids' => $rankedDriverIds]);

        if (empty($rankedDriverIds)) {
            $coordinator->failTripStaffing($trip, 'No available drivers accepted requirements.');

            return;
        }

        $handler = new SendToNextTripDriverHandler();
        $handler->handle($trip, $rankedDriverIds, 0);
    }
}
