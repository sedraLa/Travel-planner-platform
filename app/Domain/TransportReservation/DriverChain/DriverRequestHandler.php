<?php

namespace App\Domain\TransportReservation\DriverChain;

use App\Models\TransportReservation;

abstract class DriverRequestHandler
{
    protected ?DriverRequestHandler $next = null;  //each handler should know the handler after it

    //link handlers together
    public function setNext(DriverRequestHandler $next): DriverRequestHandler
    {
        $this->next = $next;

        return $next;
    }

    //call each handler in the chain
    public function handle(TransportReservation $reservation, array $rankedDriverIds, int $index = 0): void
    {
        if (!$this->process($reservation, $rankedDriverIds, $index) && $this->next) {
            $this->next->handle($reservation, $rankedDriverIds, $index + 1);
        }
    }

    //use job to send request to the driver
    abstract protected function process(TransportReservation $reservation, array $rankedDriverIds, int $index): bool;
}
