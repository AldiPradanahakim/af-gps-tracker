<?php

namespace App\Http\Requests\Home;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceRequest extends FormRequest
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

            'device_id' => [

                'required',

                'string',

                'max:100',

            ],

            'device_password' => [

                'required',

                'string',

                'max:255',

            ],

        ];
    }

    /**
     * Validation Messages.
     */
    public function messages(): array
    {
        return [

            'device_id.required' => 'ID Perangkat wajib diisi.',

            'device_password.required' => 'Kata sandi perangkat wajib diisi.',

        ];
    }
}
