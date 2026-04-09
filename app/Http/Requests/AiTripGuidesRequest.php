<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AiTripGuidesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guide_specialization_ids' => ['nullable', 'array'],
            'guide_specialization_ids.*' => ['required', 'integer', 'exists:specializations,id'],
            'requires_tour_leader' => ['nullable', 'boolean'],
        ];
    }
}
