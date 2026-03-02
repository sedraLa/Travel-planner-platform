<?php

namespace App\Domain\TransportReservation\Ranking;

use Illuminate\Support\Collection;

interface DriverRankingStrategy
{
    public function rank(Collection $drivers): Collection; //each strategy should perform this method
}
