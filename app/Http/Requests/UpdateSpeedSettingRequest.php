<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpeedSettingRequest extends FormRequest
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

            'enabled' => [
                'required',
                'boolean',
            ],

            'limit_kmh' => [
                'required',
                'integer',
                'min:1',
                'max:300',
            ],

            'email_notification' => [
                'required',
                'boolean',
            ],

            'whatsapp_notification' => [
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

            'enabled.required' => 'Status Batas Kecepatan wajib dikirim.',

            'enabled.boolean' => 'Status Batas Kecepatan tidak valid.',

            'limit_kmh.required' => 'Batas kecepatan wajib diisi.',

            'limit_kmh.integer' => 'Batas kecepatan harus berupa angka.',

            'limit_kmh.min' => 'Batas kecepatan minimal 1 km/jam.',

            'limit_kmh.max' => 'Batas kecepatan maksimal 300 km/jam.',

            'email_notification.required' => 'Status notifikasi email wajib dikirim.',

            'email_notification.boolean' => 'Status notifikasi email tidak valid.',

            'whatsapp_notification.required' => 'Status notifikasi WhatsApp wajib dikirim.',

            'whatsapp_notification.boolean' => 'Status notifikasi WhatsApp tidak valid.',

        ];
    }
}
