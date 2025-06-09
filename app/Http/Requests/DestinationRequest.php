<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DestinationRequest extends FormRequest
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
            'name' => 'required|unique:destinations,name,' . $this->route('id'), // make sure that destination name is unique
            'description' => 'nullable',
            'location_details' => 'required',
           // 'weather_info' => 'required',
            'activities' => 'nullable',
            'city' => 'required|string|max:255',
             'country' => 'required|string|max:255',
             'iata_code' => 'required|string|size:3|alpha',
             'primary_image_index' => 'nullable|integer',
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
}
