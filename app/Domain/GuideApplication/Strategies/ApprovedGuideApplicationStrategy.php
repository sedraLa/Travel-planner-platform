<?php

namespace App\Domain\GuideApplication\Strategies;

use App\Models\Guide;
use Carbon\Carbon;

class ApprovedGuideApplicationStrategy extends AbstractGuideApplicationStrategy
{
    public function apply(Guide $guide): void
    {
        $guide->update([
            'status' => $this->status(),
            'date_of_hire' => Carbon::now(),
        ]);
    }

    public function status(): string
    {
        return 'approved';
    }

    public function emailMessage(): string
    {
        return 'Your guide application has been approved! You can now start working as a guide.';
    }
}
