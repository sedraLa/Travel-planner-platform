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

    public function handle(
        DriverRankingService $rankingService,
        TripStaffingCoordinator $coordinator
    ): void {

        logger()->info('DRIVER JOB ENTERED', [
            'trip_id' => $this->tripId,
        ]);

        $trip = Trip::find($this->tripId);

        // -------------------------
        // 1. Trip missing
        // -------------------------
        if (! $trip) {
            logger()->warning('Driver job stopped: trip not found', [
                'trip_id' => $this->tripId,
            ]);
            return;
        }

        // -------------------------
        // 2. State debug BEFORE filtering
        // -------------------------
        logger()->info('Driver job state check', [
            'trip_id' => $trip->id,
            'status' => $trip->status,
            'assigned_driver_id' => $trip->assigned_driver_id,
        ]);

        // -------------------------
        // 3. Skip reasons (IMPORTANT DEBUG)
        // -------------------------
        if ($trip->assigned_driver_id) {
            logger()->info('Driver job skipped: driver already assigned', [
                'trip_id' => $trip->id,
                'assigned_driver_id' => $trip->assigned_driver_id,
            ]);
            return;
        }

        if ($trip->status !== 'staffing_in_progress') {
            logger()->info('Driver job skipped: invalid trip status', [
                'trip_id' => $trip->id,
                'status' => $trip->status,
            ]);
            return;
        }

        // -------------------------
        // 4. Ranking
        // -------------------------
        $rankedDriverIds = $rankingService->rankedDriverIdsForTrip($trip);

        logger()->info('Driver ranking result', [
            'trip_id' => $trip->id,
            'ranked_driver_ids' => $rankedDriverIds,
        ]);

        $trip->update([
            'ranked_driver_ids' => $rankedDriverIds,
        ]);

        // -------------------------
        // 5. Start chain
        // -------------------------
        if (! empty($rankedDriverIds)) {
            $handler = new SendToNextTripDriverHandler();
            $handler->handle($trip, $rankedDriverIds, 0);

            logger()->info('Driver chain started', [
                'trip_id' => $trip->id,
            ]);
        } else {
            logger()->warning('No drivers found after ranking', [
                'trip_id' => $trip->id,
            ]);
        }

        // -------------------------
        // 6. Final coordination check
        // -------------------------
        $coordinator->finalizeInitialMatchingOutcome($trip);

        logger()->info('Driver job finished', [
            'trip_id' => $trip->id,
        ]);
    }
}
