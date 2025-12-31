<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GroqTripPlannerService; // استدعاء الخدمة الجديدة

class AiTripController extends Controller
{
    protected $groqService;

    // حقن الخدمة عبر Constructor Injection
    public function __construct(GroqTripPlannerService $groqService)
    {
        $this->groqService = $groqService;
    }

    /**
     * يعرض صفحة إنشاء الرحلة بالذكاء الاصطناعي.
     */
    public function create()
    {
        return view('trips.ai.create');
    }

    /**
     * يتلقى بيانات النموذج ويتصل بـ Groq API لتوليد خطة الرحلة عبر الخدمة.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {
        // 1. التحقق من صحة المدخلات
        $validated = $request->validate([
            'description' => 'required|string|max:1000',
            'travelers_number' => 'required|integer|min:1',
            'budget' => 'nullable|numeric',
            'duration' => 'required|integer|min:1',
            // يمكن إضافة حقل لاختيار اللغة هنا
            'language' => 'nullable|in:en,ar', 
        ]);

        // 2. تجميع البيانات للخدمة
        $tripData = [
            'description' => $validated['description'],
            'travelers_number' => $validated['travelers_number'],
            'budget' => $validated['budget'],
            'duration' => $validated['duration'],
        ];
        
        $language = $validated['language'] ?? 'en'; // الافتراضي هو الإنكليزية

        // 3. استدعاء الخدمة لتوليد خطة الرحلة
        $tripPlan = $this->groqService->generateTripPlan($tripData, $language);

        // 4. معالجة الرد
        if ($tripPlan) {
            // حفظ النتيجة في الجلسة وعرضها في صفحة "عرض النتيجة"
            session()->flash('generated_trip_plan', $tripPlan);
            return redirect()->route('ai.show_result'); // يجب إنشاء هذا المسار

        } else {
            // خطأ في API أو المفتاح مفقود
            return back()->withErrors(['api_error' => 'فشل الاتصال بخدمة الذكاء الاصطناعي. تأكدي من مفتاح Groq API.']);
        }
    }
}