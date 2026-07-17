<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGeofenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Device
            |--------------------------------------------------------------------------
            */

            'device_id' => [

                'required',

                'integer',

                'exists:devices,id',

            ],

            /*
            |--------------------------------------------------------------------------
            | Geofence
            |--------------------------------------------------------------------------
            */

            'name' => [

                'required',

                'string',

                'max:100',

            ],

            'description' => [

                'nullable',

                'string',

                'max:500',

            ],

            'type' => [

                'required',

                Rule::in([

                    'radius',

                    'administrative',

                ]),

            ],

            /*
            |--------------------------------------------------------------------------
            | Radius
            |--------------------------------------------------------------------------
            */

            'source' => [

                Rule::requiredIf(
                    $this->input('type') === 'radius'
                ),

                Rule::in([

                    'home',

                    'current_location',

                ]),

            ],

            'radius' => [

                Rule::requiredIf(
                    $this->input('type') === 'radius'
                ),

                'numeric',

                'min:50',

                'max:50000',

            ],

            'radius_unit' => [

                Rule::requiredIf(
                    $this->input('type') === 'radius'
                ),

                Rule::in([

                    'meter',

                    'kilometer',

                ]),

            ],

            /*
            |--------------------------------------------------------------------------
            | Administrative
            |--------------------------------------------------------------------------
            */

            'geojson' => [

                Rule::requiredIf(
                    $this->input('type') === 'administrative'
                ),

                'array',

            ],

            'display_name' => [

                Rule::requiredIf(
                    $this->input('type') === 'administrative'
                ),

                'string',

            ],

        ];
    }

    /**
     * Custom validation message.
     */
    public function messages(): array
    {
        return [

            'device_id.required' => 'Kendaraan wajib dipilih.',

            'device_id.exists' => 'Kendaraan tidak ditemukan.',

            'name.required' => 'Nama geofence wajib diisi.',

            'type.required' => 'Jenis geofence wajib dipilih.',

            'radius.required' => 'Radius wajib diisi.',

            'radius.min' => 'Radius minimal 50 meter.',

            'radius.max' => 'Radius maksimal 50 kilometer.',

            'geojson.required' => 'Wilayah administratif wajib dipilih.',

            'display_name.required' => 'Wilayah administratif wajib dipilih.',

        ];
    }
}
