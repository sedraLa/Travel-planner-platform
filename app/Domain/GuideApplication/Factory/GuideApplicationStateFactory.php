<?php

namespace App\Domain\GuideApplication\Factory;

use App\Domain\GuideApplication\States\ApprovedGuideApplicationState;
use App\Domain\GuideApplication\States\GuideApplicationState;
use App\Domain\GuideApplication\States\RejectedGuideApplicationState;
use InvalidArgumentException;

class GuideApplicationStateFactory
{
    public function make(string $status): GuideApplicationState
    {
        return match ($status) {
            'approved' => new ApprovedGuideApplicationState(),
            'rejected' => new RejectedGuideApplicationState(),
            default => throw new InvalidArgumentException("Unsupported guide status transition: {$status}"),
        };
    }
}
