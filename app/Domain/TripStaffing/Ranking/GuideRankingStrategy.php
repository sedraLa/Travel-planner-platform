<?php

namespace App\Domain\TripStaffing\Ranking;

use Illuminate\Support\Collection;

interface GuideRankingStrategy
{
    public function rank(Collection $guides): Collection;
}
