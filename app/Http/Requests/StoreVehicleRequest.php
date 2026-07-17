<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'vehicle_name' => [
                'required',
                'string',
                'max:100',
            ],

            'vehicle_type' => [
                'required',
                'in:motor,mobil',
            ],

            'plate_number' => [
                'required',
                'string',
                'max:20',
                'unique:vehicles,plate_number',
            ],

        ];
    }
}
