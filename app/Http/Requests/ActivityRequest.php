<?php

namespace App\Http\Requests;

use App\Enums\Category;
use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'destination_id' => 'required|exists:destinations,id',
            'description' => 'nullable|string',
            'duration' => 'nullable|numeric|min:0',
            'duration_unit' => 'nullable|string|max:50',
            'price' => 'nullable|numeric|min:0',
            'category' => ['nullable', 'string', 'in:' . implode(',', Category::values())],
            'is_active' => 'required|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'availability' => 'required|string|max:50',
            'contact_number' => 'nullable|string|max:50',
            'contact_email' => ['nullable', 'email', 'max:255'],
            'requirements' => 'nullable|string',
            'difficulty_level' => 'nullable|in:easy,moderate,hard',
            // amenities JSON
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:100',
            'address' => 'required|string|max:255',
            'requires_booking' => 'required|boolean',
            'family_friendly' => ['required', 'boolean'],
            'pets_allowed' => 'required|boolean',
            'highlights' => 'nullable|string',
        ];
    }


         public function messages(): array
    {
        return [
            'contact_email.email' => 'Please enter a valid email address.',

            
        ];
    }
}
