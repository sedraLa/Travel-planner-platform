<?php

namespace App\Jobs\TripStaffing;

use App\Models\GuideRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckGuideRequestTimeoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $guideRequestId, public array $rankedGuideIds, public int $currentIndex)
    {
    }

    public function handle(): void
    {
        $request = GuideRequest::with('trip')->find($this->guideRequestId);

        if (! $request || $request->status !== 'pending') {
            return;
        }

        $request->update(['status' => 'expired']);

        if ($request->trip?->status !== 'staffing_in_progress' || $request->trip?->assigned_guide_id) {
            return;
        }

        ProcessNextGuideInChainJob::dispatchSync($request->trip_id, $this->rankedGuideIds, $this->currentIndex + 1);
    }
}
