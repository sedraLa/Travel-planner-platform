<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Trip;

class ConfirmTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            /** @var Trip $trip */
            $trip = $this->route('trip');

            if (!$trip->name || !$trip->destination_id || !$trip->duration_days) {
                $validator->errors()->add('trip', 'Trip basics are incomplete.');
            }

            if ($trip->packages()->count() < 1) {
                $validator->errors()->add('packages', 'You must add at least one package.');
            }

            if ($trip->schedules()->count() < 1) {
                $validator->errors()->add('schedules', 'You must add at least one schedule.');
            }

            if ($trip->days()->count() < 1) {
                $validator->errors()->add('days', 'Trip must have at least one day plan.');
            }

            if ($trip->images()->count() < 1) {
                $validator->errors()->add('images', 'Trip must have at least one image.');
            }
        });
    }
}