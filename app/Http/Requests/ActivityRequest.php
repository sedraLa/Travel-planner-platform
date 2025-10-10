<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'destination_id' => 'required|exists:destinations,id',
            'description' => 'nullable|string',
            'duration' => 'nullable|numeric|min:0',
            'duration_unit' => 'nullable|string|max:50',
            'price' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'is_active' => 'required|boolean',
        ];
    }
}
