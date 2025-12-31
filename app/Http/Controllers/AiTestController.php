<?php

namespace App\Http\Controllers;

use App\Services\AiTripService;

class AiTestController extends Controller
{
    public function test(AiTripService $aiTripService)
    {
        $data = [
            'duration' => 3,
            'description' => 'Trip to Paris focusing on culture and food',
            'travelers_number' => 2,
            'budget' => 'medium',
        ];

        $result = $aiTripService->generateTrip($data);

        return response()->json($result);
    }
}
