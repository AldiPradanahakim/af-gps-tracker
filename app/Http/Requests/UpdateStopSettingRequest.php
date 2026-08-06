<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStopSettingRequest extends FormRequest
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

            'stop_minutes' => [
                'required',
                Rule::in([1, 5, 10, 15]),
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

            'enabled.required' => 'Status Stop Detection wajib dikirim.',

            'enabled.boolean' => 'Status Stop Detection tidak valid.',

            'stop_minutes.required' => 'Durasi kendaraan berhenti wajib dipilih.',

            'stop_minutes.in' => 'Durasi kendaraan berhenti harus 1, 5, 10, atau 15 menit.',

            'email_notification.required' => 'Status notifikasi email wajib dikirim.',

            'email_notification.boolean' => 'Status notifikasi email tidak valid.',

            'whatsapp_notification.required' => 'Status notifikasi WhatsApp wajib dikirim.',

            'whatsapp_notification.boolean' => 'Status notifikasi WhatsApp tidak valid.',

        ];
    }
}
