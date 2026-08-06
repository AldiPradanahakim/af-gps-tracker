<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationSettingRequest extends FormRequest
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

            'email_notification.required' => 'Status notifikasi email wajib dikirim.',

            'email_notification.boolean' => 'Status notifikasi email tidak valid.',

            'whatsapp_notification.required' => 'Status notifikasi WhatsApp wajib dikirim.',

            'whatsapp_notification.boolean' => 'Status notifikasi WhatsApp tidak valid.',

        ];
    }
}
