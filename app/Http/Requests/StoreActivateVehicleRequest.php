<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActivateVehicleRequest extends FormRequest
{
    /**
     * Authorize request.
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
     * Custom Attributes.
     */
    public function attributes(): array
    {
        return [

            'vehicle_name' => 'nama kendaraan',

            'vehicle_type' => 'jenis kendaraan',

            'plate_number' => 'plat nomor',

            'marker_icon' => 'ikon marker',

            'marker_color' => 'warna marker',

        ];
    }
}
