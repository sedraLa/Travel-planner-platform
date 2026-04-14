<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AiTripPackagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'packages' => ['required', 'array', 'min:1'],
    
            'packages.*.id' => ['nullable', 'exists:trip_packages,id'],
    
            'packages.*.name' => ['required', 'string', 'max:255'],
            'packages.*.price' => ['required', 'numeric', 'min:1'],
    
            'packages.*.includes' => ['nullable'],
            'packages.*.includes.*' => ['nullable', 'string', 'max:255'],
    
            'packages.*.excludes' => ['nullable'],
            'packages.*.excludes.*' => ['nullable', 'string', 'max:255'],
    
            'packages.*.highlights' => ['nullable'],
            'packages.*.highlights.*' => ['nullable', 'string', 'max:255'],
    
            'packages.*.hotels' => ['nullable', 'array'],
    
            'packages.*.hotels.*.hotel_id' => ['nullable', 'exists:hotels,id'],
            'packages.*.hotels.*.room_type' => ['nullable', 'string', 'max:255'],
            'packages.*.hotels.*.meal_plan' => ['nullable', 'string', 'max:255'],
            'packages.*.hotels.*.amenities' => ['nullable', 'string'],
            'packages.*.hotels.*.notes' => ['nullable', 'string'],
        ];
    }
}
