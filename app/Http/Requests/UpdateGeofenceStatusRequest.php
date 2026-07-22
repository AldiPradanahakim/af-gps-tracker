<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeofenceStatusRequest extends FormRequest
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

            'status' => [
                'required',
                'boolean',
            ],

        ];
    }

    /**
     * Validation Messages
     */
    public function messages(): array
    {
        return [

            'is_active.required' =>
            'Status geofence wajib dikirim.',

            'is_active.boolean' =>
            'Status geofence tidak valid.',

        ];
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('is_active')) {

            $value = $this->input('is_active');

            if (is_string($value)) {

                $value = strtolower(trim($value));

                if (in_array($value, ['true', '1', 'on', 'yes'])) {
                    $value = true;
                } elseif (in_array($value, ['false', '0', 'off', 'no'])) {
                    $value = false;
                }
            }

            $this->merge([
                'is_active' => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }
}
