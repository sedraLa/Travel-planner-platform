<?php

namespace App\Jobs\TripStaffing;

use App\Domain\TripStaffing\GuideChain\SendToNextGuideHandler;
use App\Models\Trip;
use App\Services\TripStaffing\TripStaffingCoordinator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessNextGuideInChainJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $tripId, public array $rankedGuideIds, public int $index)
    {
    }

    public function handle(TripStaffingCoordinator $coordinator): void
    {
        $trip = Trip::find($this->tripId);

        if (! $trip || $trip->status !== 'staffing_in_progress' || $trip->assigned_guide_id) {
            return;
        }

        if (! isset($this->rankedGuideIds[$this->index])) {
            $coordinator->failTripStaffing($trip, 'All guides rejected or did not respond.');

            return;
        }

        $handler = new SendToNextGuideHandler();
        $handler->handle($trip, $this->rankedGuideIds, $this->index);
    }
}
