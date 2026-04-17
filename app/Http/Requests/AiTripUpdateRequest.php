<?php

namespace App\Http\Requests;

use App\Enums\Category;
use Illuminate\Foundation\Http\FormRequest;

class AiTripUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destination_ids' => ['required', 'array', 'min:1'],
            'destination_ids.*' => ['required', 'integer', 'exists:destinations,id'],
            'description' => ['required', 'string', 'max:1000'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['required', 'string', 'in:' . implode(',', Category::values())],
            'max_participants' => ['required', 'integer', 'min:1'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'duration' => ['required', 'integer', 'min:1', 'max:30'],
            'language' => ['nullable', 'in:en,ar'],
        ];
    }
}
