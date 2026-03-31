<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuideAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_id' => 'required|exists:trips,id',
            'guide_id' => 'required|exists:guides,id',
            'status' => 'nullable|string|in:pending,accepted,rejected,cancelled',
        ];
    }
}
