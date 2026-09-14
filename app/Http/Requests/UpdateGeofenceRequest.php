<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeofenceRequest extends FormRequest
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

            'status' => [
                'required',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | Radius (opsional, hanya dipakai kalau titik/radius diubah)
            |--------------------------------------------------------------------------
            */

            'radius_source' => [
                'nullable',
                'string',
                'in:keep_current,home_location,current_location,manual',
            ],

            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],

            'radius' => [
                'nullable',
                'numeric',
                'min:50',
                'max:50000',
            ],

            /*
            |--------------------------------------------------------------------------
            | Administrative (opsional, hanya dipakai kalau wilayah diubah)
            |--------------------------------------------------------------------------
            */

            'display_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'administrative_type' => [
                'nullable',
                'string',
                'max:100',
            ],

            /*
            |--------------------------------------------------------------------------
            | Administrative & Custom (opsional, hanya dipakai kalau geometry diubah)
            |--------------------------------------------------------------------------
            */

            'geojson' => [
                'nullable',
                'json',
                'max:500000',
            ],

        ];
    }

    /**
     * Validation Messages
     */
    public function messages(): array
    {
        return [

            'name.required' => 'Nama geofence wajib diisi.',

            'name.max' => 'Nama geofence maksimal 100 karakter.',

            'status.required' => 'Status geofence wajib dipilih.',

            'status.boolean' => 'Status geofence tidak valid.',

            'radius_source.in' => 'Sumber titik pusat tidak dikenali.',

            'latitude.numeric' => 'Lintang tidak valid.',

            'longitude.numeric' => 'Bujur tidak valid.',

            'radius.min' => 'Radius minimal 50 meter.',

            'radius.max' => 'Radius maksimal 50000 meter.',

            'geojson.json' => 'Format GeoJSON tidak valid.',

        ];
    }
}
