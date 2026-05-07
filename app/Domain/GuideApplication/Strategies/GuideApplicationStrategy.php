<?php

namespace App\Domain\GuideApplication\Strategies;

use App\Models\Guide;

interface GuideApplicationStrategy
{
    //implementation for each state
    public function apply(Guide $guide): void;

    //return status name
    public function status(): string;

    public function emailMessage(): string;

    public function shouldDeleteGuide(): bool;
}
