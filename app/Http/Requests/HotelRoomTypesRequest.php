<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelRoomTypesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_types' => 'nullable|array',
            'room_types.*.id' => 'nullable|integer|exists:room_types,id',
            'room_types.*.name' => 'required_with:room_types|string|max:255',
            'room_types.*.price_per_night' => 'required_with:room_types|numeric|gt:0',
            'room_types.*.capacity' => 'required_with:room_types|integer|gt:0',
            'room_types.*.quantity' => 'required_with:room_types|integer|min:0',
            'room_types.*.description' => 'nullable|string',
            'room_types.*.amenities' => 'nullable|string',
            'room_types.*.is_refundable' => 'nullable|boolean',
            'room_types.*.images' => 'nullable|array',
            'room_types.*.images.*' => 'image|mimes:jpeg,png,jpg,gif',
            'room_types.*.primary_image_choice' => 'nullable|string',
            'room_types.*.primary_new_image_choice' => 'nullable|string',
            'room_types.*.remove_existing_image_ids' => 'nullable|array',
            'room_types.*.remove_existing_image_ids.*' => 'integer|exists:room_type_images,id',
        ];
    }
}
