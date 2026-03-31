<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TripHighlightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip_package_id' => 'nullable|exists:trip_packages,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
