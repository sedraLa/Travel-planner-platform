<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GroqTripPlannerService;
use App\Models\Trip;
use Carbon\Carbon; // تأكدي من وجود هذا السطر

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
        // 1. التحقق من صحة المدخلات (بما في ذلك التواريخ)
        $validated = $request->validate([
            'description' => 'required|string|max:1000',
            'travelers_number' => 'required|integer|min:1',
            'budget' => 'nullable|numeric',
            'duration' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'language' => 'nullable|in:en,ar',
        ]);

        // 2. منطق التواريخ الذكي
        // إذا لم يدخل تاريخ البداية، نأخذ تاريخ اليوم
        $startDate = $validated['start_date'] ?? now()->toDateString();
        
        // إذا لم يدخل تاريخ النهاية، نحسبه تلقائياً: البداية + (المدة - 1)
        if (empty($validated['end_date'])) {
            $endDate = Carbon::parse($startDate)->addDays($validated['duration'] - 1)->toDateString();
        } else {
            $endDate = $validated['end_date'];
        }

        // 3. استدعاء الخدمة لتوليد الرحلة
        $language = $validated['language'] ?? 'en';
        $tripPlan = $this->groqService->generateTripPlan($validated, $language);

        // 4. الحفظ في قاعدة البيانات
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

            return redirect()->route('trip.show', $trip->id)->with('success', 'Your AI trip has been generated and saved!');
        }

        return back()->withErrors(['api_error' => 'Failed to generate trip. Please check your API key.']);
    }

    public function show(Trip $trip)
    {
        return view('trips.ai.show', compact('trip'));
    }
}