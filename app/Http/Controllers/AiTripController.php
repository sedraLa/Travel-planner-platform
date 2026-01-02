<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GroqTripPlannerService;
use App\Models\Trip;
use Carbon\Carbon; 

class AiTripController extends Controller
{
    protected $groqService;

    public function __construct(GroqTripPlannerService $groqService)
    {
        $this->groqService = $groqService;
    }

    public function create()
    {
        return view('trips.ai.create');
    }

    public function generate(Request $request)
    {
        
        $validated = $request->validate([
            'description' => 'required|string|max:1000',
            'travelers_number' => 'required|integer|min:1',
            'budget' => 'nullable|numeric',
            'duration' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'language' => 'nullable|in:en,ar',
        ]);

        
       
        $startDate = $validated['start_date'] ?? now()->toDateString();
        
       
        if (empty($validated['end_date'])) {
            $endDate = Carbon::parse($startDate)->addDays($validated['duration'] - 1)->toDateString();
        } else {
            $endDate = $validated['end_date'];
        }

        
        $language = $validated['language'] ?? 'en';
        $tripPlan = $this->groqService->generateTripPlan($validated, $language);

        
        if ($tripPlan) {
            $trip = Trip::create([
                'user_id' => auth()->id(),
                'is_ai' => true,
                'name' => 'AI Trip: ' . substr($validated['description'], 0, 30) . '...',
                'description' => $validated['description'],
                'travelers_number' => $validated['travelers_number'],
                'budget' => $validated['budget'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'ai_itinerary' => $tripPlan,
            ]);

            return redirect()->route('ai.show', $trip->id)->with('success', 'Your AI trip has been generated and saved!');
        }

        return back()->withErrors(['api_error' => 'Failed to generate trip. Please check your API key.']);
    }

    public function show(Trip $trip)
    {
        return view('trips.ai.show', compact('trip'));
    }
}