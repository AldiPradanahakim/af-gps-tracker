<?php

namespace App\Http\Requests;

use App\Models\Device;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGeofenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }
    /**
     * Validation Rules
     */
    public function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Basic
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
                'max:255',
            ],

            'device_id' => [
                'required',
                function ($attribute, $value, $fail) {

                    if ($value === 'all') {
                        return;
                    }

                    if (
                        ! is_numeric($value) ||
                        ! Device::whereKey($value)->exists()
                    ) {
                        $fail('Kendaraan tidak ditemukan.');
                    }
                },
            ],

            /*
            |--------------------------------------------------------------------------
            | Type
            |--------------------------------------------------------------------------
            */

            'type' => [
                'required',
                Rule::in([
                    'radius',
                    'administrative',
                    'custom',
                ]),
            ],

            /*
            |--------------------------------------------------------------------------
            | Radius
            |--------------------------------------------------------------------------
            */

            'radius_source' => [
                'required_if:type,radius',
                Rule::in([
                    'home_location',
                    'current_location',
                    'manual',
                ]),
            ],

            'latitude' => [
                'required_if:radius_source,manual',
                'nullable',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'required_if:radius_source,manual',
                'nullable',
                'numeric',
                'between:-180,180',
            ],

            'radius' => [
                'required_if:type,radius',
                'nullable',
                'numeric',
                'min:50',
                'max:50000',
            ],

            'radius_unit' => [
                'required_if:type,radius',
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

            'display_name' => [
                'required_if:type,administrative',
                'nullable',
                'string',
                'max:255',
            ],

            'administrative_type' => [
                'required_if:type,administrative',
                'nullable',
                'string',
                'max:100',
            ],

            /*
            |--------------------------------------------------------------------------
            | Administrative & Custom
            |--------------------------------------------------------------------------
            */

            'geojson' => [
                'required_if:type,administrative,custom',
                'nullable',
                'json',
            ],

        ];
    }

    /**
     * Validation Messages
     */
    public function messages(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Basic
            |--------------------------------------------------------------------------
            */

            'name.required' => 'Nama geofence wajib diisi.',

            'name.max' => 'Nama geofence maksimal 100 karakter.',

            'description.max' => 'Deskripsi maksimal 255 karakter.',

            'device_id.required' => 'Silakan pilih kendaraan.',

            'device_id.exists' => 'Kendaraan tidak ditemukan.',

            /*
            |--------------------------------------------------------------------------
            | Type
            |--------------------------------------------------------------------------
            */

            'type.required' => 'Jenis geofence wajib dipilih.',

            'type.in' => 'Jenis geofence tidak valid.',

            /*
            |--------------------------------------------------------------------------
            | Radius
            |--------------------------------------------------------------------------
            */

            'radius_source.required_if' =>
            'Sumber titik radius wajib dipilih.',

            'radius.required_if' =>
            'Radius wajib diisi.',

            'radius.min' =>
            'Radius minimal 50 meter.',

            'radius.max' =>
            'Radius maksimal 50000 meter.',

            'radius.numeric' =>
            'Radius harus berupa angka.',

            'radius_unit.required_if' =>
            'Satuan radius wajib dipilih.',

            'radius_unit.in' =>
            'Satuan radius tidak valid.',

            'latitude.required_if' =>
            'Latitude wajib diisi.',

            'longitude.required_if' =>
            'Longitude wajib diisi.',

            'latitude.numeric' =>
            'Latitude tidak valid.',

            'longitude.numeric' =>
            'Longitude tidak valid.',

            /*
            |--------------------------------------------------------------------------
            | Administrative
            |--------------------------------------------------------------------------
            */

            'display_name.required_if' =>
            'Wilayah administratif wajib dipilih.',

            'administrative_type.required_if' =>
            'Jenis wilayah administratif wajib dipilih.',

            /*
            |--------------------------------------------------------------------------
            | GeoJSON
            |--------------------------------------------------------------------------
            */

            'geojson.required_if' =>
            'GeoJSON wajib tersedia.',

            'geojson.json' =>
            'Format GeoJSON tidak valid.',

        ];
    }

    /**
     * Prepare Before Validation
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('type')) {
            $this->merge([
                'type' => strtolower(trim($this->type)),
            ]);
        }

        if ($this->filled('radius_unit')) {
            $this->merge([
                'radius_unit' => strtolower(trim($this->radius_unit)),
            ]);
        }

        if ($this->filled('radius_source')) {
            $this->merge([
                'radius_source' => strtolower(trim($this->radius_source)),
            ]);
        }
    }
}
