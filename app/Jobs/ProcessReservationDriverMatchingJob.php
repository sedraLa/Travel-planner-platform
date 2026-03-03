<?php

namespace App\Jobs;

use App\Domain\TransportReservation\DriverChain\SendToNextDriverHandler;
use App\Models\TransportReservation;
use App\Services\TransportReservation\DriverRankingService;
use App\Services\TransportReservation\ReservationStateManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessReservationDriverMatchingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $reservationId)
    {
    }

    public function handle(DriverRankingService $rankingService, ReservationStateManager $stateManager): void
    {
        $reservation = TransportReservation::find($this->reservationId);

        if (!$reservation || $reservation->status === 'cancelled') {
            return;
        }

        if ($reservation->status === 'pending_payment') {
            $stateManager->transition($reservation, 'pending_driver');
        }

        $rankedDriverIds = $rankingService->rankedDriverIdsForReservation($reservation);
        $reservation->update(['ranked_driver_ids' => $rankedDriverIds]);

        $handler = new SendToNextDriverHandler();
        $handler->handle($reservation, $rankedDriverIds, 0);
    }
}