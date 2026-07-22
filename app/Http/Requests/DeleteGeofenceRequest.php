<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteGeofenceRequest extends FormRequest
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

            'geofence_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'geofence_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:geofences,id',
            ],

        ];
    }

    /**
     * Validation Messages
     */
    public function messages(): array
    {
        return [

            'geofence_ids.required' =>
            'Pilih minimal satu geofence.',

            'geofence_ids.array' =>
            'Format data geofence tidak valid.',

            'geofence_ids.min' =>
            'Pilih minimal satu geofence.',

            'geofence_ids.*.required' =>
            'ID geofence wajib diisi.',

            'geofence_ids.*.integer' =>
            'ID geofence tidak valid.',

            'geofence_ids.*.distinct' =>
            'Terdapat ID geofence yang duplikat.',

            'geofence_ids.*.exists' =>
            'Geofence tidak ditemukan.',

        ];
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void
    {
        if (!is_array($this->geofence_ids)) {
            $this->merge([
                'geofence_ids' => [],
            ]);
        }

        $this->merge([
            'geofence_ids' => array_values(
                array_unique(
                    array_filter(
                        $this->geofence_ids
                    )
                )
            ),
        ]);
    }
}
