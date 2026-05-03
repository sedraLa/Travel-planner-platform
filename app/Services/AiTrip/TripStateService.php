<?php

namespace App\Services\AiTrip;

use App\Jobs\TripStaffing\StartTripStaffingJob;
use App\Models\Trip;
use App\Services\Trip\TripStateManager;
use Illuminate\Support\Facades\DB;

class TripStateService
{
    public function __construct(protected TripStateManager $tripStateManager)
    {
    }

    public function confirmOverview(Trip $trip): void
    {
        DB::transaction(function () use ($trip) {
            $trip->refresh();

            if ($trip->status === 'staffing_in_progress' || $trip->status === 'staffed' || $trip->status === 'published') {
                return;
            }

            if ($trip->status === 'draft') {
                $this->tripStateManager->transition($trip, 'ready_for_assignment');
            }

            if ($trip->fresh()->status === 'ready_for_assignment') {
                StartTripStaffingJob::dispatch($trip->id)->afterCommit();
            }
        });
    }
}
