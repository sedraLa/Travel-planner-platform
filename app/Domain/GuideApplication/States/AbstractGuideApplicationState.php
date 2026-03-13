<?php

namespace App\Domain\GuideApplication\States;

abstract class AbstractGuideApplicationState implements GuideApplicationState
{
    public function shouldDeleteGuide(): bool
    {
        return false;
    }
}
