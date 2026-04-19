<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Trip;
use App\Services\AiAssistant\GroqEntityAssistantService;
use App\Services\AiAssistant\Prompts\ActivityPromptService;
use App\Services\AiAssistant\Prompts\DestinationPromptService;
use App\Services\AiAssistant\Prompts\HotelPromptService;
use App\Services\AiAssistant\Prompts\TripPromptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiAssistantController extends Controller
{
    public function __construct(
        protected GroqEntityAssistantService $groqService,
        protected HotelPromptService $hotelPromptService,
        protected DestinationPromptService $destinationPromptService,
        protected ActivityPromptService $activityPromptService,
        protected TripPromptService $tripPromptService,
    ) {
    }

    public function askHotel(Request $request): JsonResponse
    {
        $data = $request->validate([
            'entity_id' => ['required', 'integer', 'exists:hotels,id'],
            'question' => ['required', 'string', 'max:1000'],
        ]);

        $hotel = Hotel::query()->with('destination')->findOrFail($data['entity_id']);

        return $this->respond(
            $this->hotelPromptService->systemMessage(),
            $this->hotelPromptService->userMessage($hotel, $data['question'])
        );
    }

    public function askDestination(Request $request): JsonResponse
    {
        $data = $request->validate([
            'entity_id' => ['required', 'integer', 'exists:destinations,id'],
            'question' => ['required', 'string', 'max:1000'],
        ]);

        $destination = Destination::query()
            ->with('highlights:id,destination_id,title')
            ->findOrFail($data['entity_id']);

        return $this->respond(
            $this->destinationPromptService->systemMessage(),
            $this->destinationPromptService->userMessage($destination, $data['question'])
        );
    }

    public function askActivity(Request $request): JsonResponse
    {
        $data = $request->validate([
            'entity_id' => ['required', 'integer', 'exists:activities,id'],
            'question' => ['required', 'string', 'max:1000'],
        ]);

        $activity = Activity::query()
            ->with(['destination', 'highlights:id,activity_id,title'])
            ->findOrFail($data['entity_id']);

        return $this->respond(
            $this->activityPromptService->systemMessage(),
            $this->activityPromptService->userMessage($activity, $data['question'])
        );
    }

    public function askTrip(Request $request): JsonResponse
    {
        $data = $request->validate([
            'entity_id' => ['required', 'integer', 'exists:trips,id'],
            'question' => ['required', 'string', 'max:1000'],
        ]);

        $trip = Trip::query()
            ->with([
                'itineraryDestinations:id,name',
                'packages:id,trip_id,name,price',
            ])
            ->findOrFail($data['entity_id']);

        return $this->respond(
            $this->tripPromptService->systemMessage(),
            $this->tripPromptService->userMessage($trip, $data['question'])
        );
    }

    protected function respond(string $systemMessage, string $userMessage): JsonResponse
    {
        $answer = $this->groqService->ask($systemMessage, $userMessage);

        if (blank($answer)) {
            return response()->json([
                'answer' => 'I couldn\'t generate an answer right now. Please try again in a moment, or ask your question in a different way.',
            ], 503);
        }

        return response()->json([
            'answer' => $answer,
        ]);
    }
}
