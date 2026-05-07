<?php

namespace App\Domain\GuideApplication\Factory;

use App\Domain\GuideApplication\Strategies\ApprovedGuideApplicationStrategy;
use App\Domain\GuideApplication\Strategies\GuideApplicationStrategy;
use App\Domain\GuideApplication\Strategies\RejectedGuideApplicationStrategy;
use InvalidArgumentException;

class GuideApplicationStrategyFactory
{
    public function make(string $status): GuideApplicationStrategy
    {
        return match ($status) {
            'approved' => new ApprovedGuideApplicationStrategy(),
            'rejected' => new RejectedGuideApplicationStrategy(),
            default => throw new InvalidArgumentException("Unsupported guide status: {$status}"),
        };
    }
}
