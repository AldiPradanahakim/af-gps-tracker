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
                'regex:/^[A-Z]{1,2}\s\d{1,4}\s[A-Z]{1,3}$/i',
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

    /**
     * Custom Messages.
     */
    public function messages(): array
    {
        return [

            'vehicle_name.required' => 'Nama kendaraan wajib diisi.',
            'vehicle_name.max' => 'Nama kendaraan maksimal 100 karakter.',

            'vehicle_type.required' => 'Jenis kendaraan wajib dipilih.',
            'vehicle_type.in' => 'Jenis kendaraan tidak valid.',

            'plate_number.required' => 'Nomor polisi wajib diisi.',
            'plate_number.max' => 'Nomor polisi maksimal 20 karakter.',
            'plate_number.regex' => 'Format nomor polisi tidak sesuai. Gunakan format Huruf-Angka-Huruf, contoh: D 1234 ABC.',

            'marker_icon.required' => 'Ikon marker wajib dipilih.',
            'marker_icon.in' => 'Ikon marker tidak valid.',

            'marker_color.required' => 'Warna marker wajib dipilih.',
            'marker_color.in' => 'Warna marker tidak valid.',

        ];
    }
}
