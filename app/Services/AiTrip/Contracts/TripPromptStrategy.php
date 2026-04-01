<?php

namespace App\Services\AiTrip\Contracts;

interface TripPromptStrategy
{
    public function language(): string;

    public function systemMessage(): string;

    public function userMessage(array $tripData, array $catalog): string;
}
