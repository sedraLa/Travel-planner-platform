<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuideAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
          return $this->user()?->role === 'guide' && $this->user()?->guide !== null;
    }

     protected function prepareForValidation(): void
    {
        if ($this->user()?->guide) {
            $this->merge([
                'guide_id' => $this->user()->guide->id,
            ]);
        }
    }
    public function rules(): array
    {
        return [
            'guide_id' => 'required|exists:guides,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ];
    }
}
