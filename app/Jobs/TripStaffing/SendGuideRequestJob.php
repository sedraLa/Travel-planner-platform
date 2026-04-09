<?php

namespace App\Jobs\TripStaffing;

use App\Models\Guide;
use App\Models\GuideRequest;
use App\Models\Trip;
use App\Notifications\GuideStaffingRequestNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Queue;

class SendGuideRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $tripId,
        public int $guideId,
        public array $rankedGuideIds,
        public int $currentIndex,
    ) {
    }

    public function handle(): void
    {
        $trip = Trip::find($this->tripId);
        $guide = Guide::with('user')->find($this->guideId);

        if (! $trip || ! $guide || $trip->status !== 'staffing_in_progress' || $trip->assigned_guide_id) {
            return;
        }

        $request = GuideRequest::create([
            'trip_id' => $trip->id,
            'guide_id' => $guide->id,
            'chain_index' => $this->currentIndex,
            'status' => 'pending',
            'expires_at' => now()->addDay(),
        ]);

        if ($guide->user) {
            $guide->user->notify(new GuideStaffingRequestNotification($trip));
        }

        if (Queue::getDefaultDriver() !== 'sync') {
            CheckGuideRequestTimeoutJob::dispatch($request->id, $this->rankedGuideIds, $this->currentIndex)
                ->delay($request->expires_at);
        }
    }
}
