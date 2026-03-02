<?php

namespace App\Domain\TransportReservation\DriverChain;

use App\Jobs\SendBookingRequestToDriverJob;
use App\Models\TransportReservation;

class SendToNextDriverHandler extends DriverRequestHandler
{
    protected function process(TransportReservation $reservation, array $rankedDriverIds, int $index): bool
    {
        if (!isset($rankedDriverIds[$index])) {
            $reservation->update(['status' => 'cancelled']);

            return true;
        }

        //send request to the handler(driver) u
        SendBookingRequestToDriverJob::dispatchSync($reservation->id, $rankedDriverIds[$index], $rankedDriverIds, $index);

        return true;
    }
}
