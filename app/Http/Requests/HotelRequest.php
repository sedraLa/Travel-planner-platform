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
        $rules = [
            'name' => 'required|string|max:255|unique:hotels,name,' . $this->route('id'),
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'price_per_night' => 'required|numeric|min:0',
            'global_rating' => 'nullable|integer|min:1|max:5',
            'total_rooms' => 'required|integer|min:1',
            'destination_id' => 'required|exists:destinations,id',
            'primary_image_index' => 'nullable|integer|min:0',
        ];

        if($this->isMethod('post')) {
            ///create
            $rules['images'] = 'required|array|min:1';
            $rules['images.*']  = 'image|mimes:jpeg,png,jpg,gif';
        } else {
            ///edit
            $rules['images'] = 'nullable|array';
            $rules['images.*'] = 'image|mimes:jpeg,png,jpg,gif';
        }
        return $rules;
    }
    /**public function messages():array{
     * return [
     * 'name.required'=>''
     * ]
    } */
}
