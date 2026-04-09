<?php

namespace App\Jobs\TripStaffing;

use App\Models\DriverRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckDriverRequestTimeoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $driverRequestId, public array $rankedDriverIds, public int $currentIndex)
    {
    }

    public function handle(): void
    {
        $request = DriverRequest::with('trip')->find($this->driverRequestId);

        if (! $request || $request->status !== 'pending') {
            return;
        }

        $request->update(['status' => 'expired']);

        if ($request->trip?->status !== 'staffing_in_progress' || $request->trip?->assigned_driver_id) {
            return;
        }

        ProcessNextDriverInChainJob::dispatchSync($request->trip_id, $this->rankedDriverIds, $this->currentIndex + 1);
    }
}
