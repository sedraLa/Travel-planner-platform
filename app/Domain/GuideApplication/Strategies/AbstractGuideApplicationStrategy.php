<?php

namespace App\Domain\GuideApplication\Strategies;

abstract class AbstractGuideApplicationStrategy implements GuideApplicationStrategy
{
    public function shouldDeleteGuide(): bool
    {
        return false;
    }
}
