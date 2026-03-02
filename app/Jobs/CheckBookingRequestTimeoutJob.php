<?php

namespace App\Jobs;

use App\Models\BookingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckBookingRequestTimeoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $bookingRequestId, public array $rankedDriverIds, public int $currentIndex)
    {
    }

    public function handle(): void
    {
        $bookingRequest = BookingRequest::with('reservation')->find($this->bookingRequestId);

        if (!$bookingRequest || $bookingRequest->status !== 'pending') {
            return;
        }

        $bookingRequest->update(['status' => 'expired']);

        ProcessNextDriverInChainJob::dispatch($bookingRequest->reservation_id, $this->rankedDriverIds, $this->currentIndex + 1);
    }
}
