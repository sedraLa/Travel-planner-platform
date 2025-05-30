<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:hotels,name',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'price_per_night' => 'required|numeric|min:0',
            'global_rating' => 'nullable|integer|min:1|max:5',
            'total_rooms' => 'required|integer|min:1',
            'destination_id' => 'required|exists:destinations,id',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif',
            'primary_image_index' => 'nullable|integer|min:0',
        ];
    }
}
