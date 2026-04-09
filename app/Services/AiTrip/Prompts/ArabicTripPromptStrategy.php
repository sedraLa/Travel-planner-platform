<?php

namespace App\Services\AiTrip\Prompts;

use App\Services\AiTrip\Contracts\TripPromptStrategy;

class ArabicTripPromptStrategy implements TripPromptStrategy
{
    public function language(): string
    {
        return 'ar';
    }

    public function systemMessage(): string //rules
    {
        return 'أنت مخطط رحلات ويجب أن تلتزم فقط ببيانات الكتالوج المرسلة من قاعدة البيانات. ممنوع اختراع أي بيانات. أعد JSON صالح فقط.';
    }

    public function userMessage(array $tripData, array $catalog): string
    {
        $catalogJson = json_encode($catalog, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); //convert data to json
        $selectedCategories = implode('، ', $tripData['categories'] ?? []);
        $selectedDestinations = implode('، ', array_map('strval', $tripData['destination_ids'] ?? []));

        return <<<PROMPT
أنشئ خطة رحلة لمدة {$tripData['duration']} يوم باللغة العربية بالاعتماد فقط على معرفات البيانات الموجودة في الكتالوج.

مدخلات المستخدم:
- destination_ids (اختيار متعدد): {$selectedDestinations}
- الوصف: {$tripData['description']}
- تصنيفات الرحلة (اختيار متعدد): {$selectedCategories}
- الحد الأقصى للمشاركين: {$tripData['max_participants']}
- الميزانية: {$tripData['budget']}

قواعد صارمة:
1) استخدم فقط destination_id و hotel_id و activity_id الموجودة في الكتالوج.
2) ممنوع توليد بيانات الباقات مثل includes/excludes/highlights/info أو بيانات النقل trip_transports.
3) ممنوع توليد meeting_point_description أو meeting_point_address لأنها تدار من الأدمن عبر الخريطة وخدمة geocoding.
4) إذا لم تجد خياراً مناسباً لا تخترع بيانات، وتجاوز العنصر.
5) ركّز على أحدث البيانات (updated_at الأحدث).
6) عدد الأيام داخل "days" يجب أن يساوي تماماً {$tripData['duration']}.
7) وصف كل يوم لازم يكون غني ومفصل (تنظيم اليوم، نصائح، مبرر ترتيب الأنشطة) وبحد أدنى 35 كلمة.
8) قواعد توزيع الفنادق:
   - إذا كانت الرحلة أطول من 5 أيام، بدّل الفندق بين عدة أيام عند توفر خيارات مناسبة.
   - إذا كانت الأنشطة متباعدة بحسب المنطقة/العنوان، اختر فندق أقرب لذلك اليوم.
   - إذا كانت الرحلة تشمل أكثر من وجهة مختارة، غيّر الفندق ليتوافق مع تسلسل الوجهات.
9) اقتراحات الذكاء الاصطناعي للفنادق تكون على مستوى أيام الرحلة فقط، أما اختلافات الباقات (room type, amenities, meal plan, notes/highlights) فهي تُدار من الأدمن لاحقاً.
10) يجب أن يكون الخرج JSON مطابق تماماً للبنية التالية:
{
  "trip_name": "string",
  "trip_description": "string",
  "days": [
    {
      "day_number": 1,
      "title": "string",
      "description": "string",
      "hotel_id": 1,
      "activities": [
        {
          "activity_id": 1,
          "start_time": "09:00",
          "end_time": "11:00",
          "notes": "string"
        }
      ]
    }
  ],
  "markdown_summary": "string"
}

الكتالوج:
{$catalogJson}
PROMPT;
    }
}
