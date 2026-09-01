<?php

namespace App\Http\Requests\Home;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehicleRequest extends FormRequest
{
    /**
     * Determine whether the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }
    /**
     * Validation Rules.
     */
    public function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Vehicle
            |--------------------------------------------------------------------------
            */

            'vehicle_name' => [

                'required',

                'string',

                'max:100',

            ],

            'vehicle_type' => [

                'required',

                Rule::in([

                    'motor',
                    'mobil',
                    'kendaraan_besar',
                    'sepeda',

                ]),

            ],

            'plate_number' => [

                'required',

                'string',

                'max:20',

            ],

            /*
            |--------------------------------------------------------------------------
            | Marker
            |--------------------------------------------------------------------------
            */

            'marker_icon' => [

                'required',

                Rule::in([

                    'motorcycle',
                    'car',
                    'pickup',
                    'truck',
                    'bus',
                    'van',
                    'taxi',
                    'ambulance',
                    'police',
                    'bicycle',

                ]),

            ],

            'marker_color' => [

                'required',

                Rule::in([

                    'green',
                    'blue',
                    'red',
                    'orange',
                    'yellow',
                    'purple',
                    'black',
                    'gray',

                ]),

            ],

        ];
    }

    /**
     * Validation Messages.
     */
    public function messages(): array
    {
        return [

            'vehicle_name.required' => 'Nama kendaraan wajib diisi.',

            'vehicle_type.required' => 'Jenis kendaraan wajib dipilih.',

            'vehicle_type.in' => 'Jenis kendaraan tidak valid.',

            'plate_number.required' => 'Plat nomor wajib diisi.',

            'marker_icon.required' => 'Icon marker wajib dipilih.',

            'marker_color.required' => 'Warna marker wajib dipilih.',

        ];
    }
}
