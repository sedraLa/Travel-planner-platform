<?php

namespace App\Jobs\TripStaffing;

use App\Models\Driver;
use App\Models\DriverRequest;
use App\Models\Trip;
use App\Notifications\DriverStaffingRequestNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Queue;

class SendDriverRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $tripId,
        public int $driverId,
        public array $rankedDriverIds,
        public int $currentIndex,
    ) {
    }

    public function handle(): void
    {
        $trip = Trip::find($this->tripId);
        $driver = Driver::with('user')->find($this->driverId);

        if (! $trip || ! $driver || $trip->status !== 'staffing_in_progress' || $trip->assigned_driver_id) {
            return;
        }

        $request = DriverRequest::create([
            'trip_id' => $trip->id,
            'driver_id' => $driver->id,
            'chain_index' => $this->currentIndex,
            'status' => 'pending',
            'expires_at' => now()->addDay(),
        ]);

        if ($driver->user) {
            $driver->user->notify(new DriverStaffingRequestNotification($trip));
        }

        if (Queue::getDefaultDriver() !== 'sync') {
            CheckDriverRequestTimeoutJob::dispatch($request->id, $this->rankedDriverIds, $this->currentIndex)
                ->delay($request->expires_at);
        }
    }
}
