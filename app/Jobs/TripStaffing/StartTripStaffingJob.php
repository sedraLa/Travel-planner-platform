<?php

namespace App\Jobs\TripStaffing;

use App\Models\Trip;
use App\Services\Trip\TripStateManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StartTripStaffingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $tripId)
    {
    }

    public function handle(TripStateManager $stateManager): void
    {
        $trip = Trip::find($this->tripId);

        if (! $trip || $trip->status !== 'ready_for_assignment') {
            return;
        }

        $stateManager->transition($trip, 'staffing_in_progress');

        ProcessTripGuideMatchingJob::dispatch($trip->id);
    }
}
