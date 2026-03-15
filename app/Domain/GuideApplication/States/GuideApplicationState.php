<?php

namespace App\Domain\GuideApplication\States;

use App\Models\Guide;

interface GuideApplicationState
{
    public function apply(Guide $guide): void;

    public function status(): string;

    public function emailMessage(): string;

    public function shouldDeleteGuide(): bool;
}
