<?php

namespace App\Jobs\TripStaffing;

use App\Domain\TripStaffing\GuideChain\SendToNextGuideHandler;
use App\Models\Trip;
use App\Services\TripStaffing\GuideRankingService;
use App\Services\TripStaffing\TripStaffingCoordinator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTripGuideMatchingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $tripId)
    {
    }

    public function handle(GuideRankingService $rankingService, TripStaffingCoordinator $coordinator): void
    {
        $trip = Trip::find($this->tripId);

        if (! $trip || $trip->status !== 'staffing_in_progress' || $trip->assigned_guide_id) {
            return;
        }

        $rankedGuideIds = $rankingService->rankedGuideIdsForTrip($trip);
        $trip->update(['ranked_guide_ids' => $rankedGuideIds]);

        if (! empty($rankedGuideIds)) {
            $handler = new SendToNextGuideHandler();
            $handler->handle($trip, $rankedGuideIds, 0);
        }

        $coordinator->finalizeInitialMatchingOutcome($trip);
    }
}
