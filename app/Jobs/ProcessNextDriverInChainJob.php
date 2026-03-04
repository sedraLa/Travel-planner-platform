<?php

namespace App\Jobs;

use App\Domain\TransportReservation\DriverChain\SendToNextDriverHandler;
use App\Models\TransportReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessNextDriverInChainJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $reservationId, public array $rankedDriverIds, public int $index)
    {
    }

    public function handle(): void
    {
        $reservation = TransportReservation::find($this->reservationId);

        if (!$reservation || $reservation->status !== 'pending_driver') {
            return;
        }

        $handler = new SendToNextDriverHandler();
        $handler->handle($reservation, $rankedDriverIds = $this->rankedDriverIds, $this->index);
    }
}
