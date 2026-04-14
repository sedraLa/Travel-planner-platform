<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AiTripBasicsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destination_id' => ['required', 'exists:destinations,id'],
            'destination_ids' => ['required', 'array', 'min:1'],
            'destination_ids.*' => ['required', 'integer', 'exists:destinations,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:30'],
            'category' => ['nullable', 'string', 'max:255'],
            'max_participants' => ['required', 'integer', 'min:1'],
            'meeting_point_description' => ['nullable', 'string'],
            'meeting_point_address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
