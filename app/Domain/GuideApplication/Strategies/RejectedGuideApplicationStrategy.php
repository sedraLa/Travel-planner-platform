<?php

namespace App\Domain\GuideApplication\Strategies;

use App\Models\Guide;

class RejectedGuideApplicationStrategy extends AbstractGuideApplicationStrategy
{
    public function apply(Guide $guide): void
    {
        $guide->update([
            'status' => $this->status(),
        ]);
    }

    public function status(): string
    {
        return 'rejected';
    }

    public function emailMessage(): string
    {
        return 'Sorry, your guide application has been rejected after reviewing your information.';
    }

    public function shouldDeleteGuide(): bool
    {
        return true;
    }
}
