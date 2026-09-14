<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
                'integer',
                'min:1',
                'max:1440',
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

            'enabled.required' => 'Status Deteksi Berhenti wajib dikirim.',

            'enabled.boolean' => 'Status Deteksi Berhenti tidak valid.',

            'stop_minutes.required' => 'Durasi kendaraan berhenti wajib diisi.',

            'stop_minutes.integer' => 'Durasi kendaraan berhenti harus berupa angka.',

            'stop_minutes.min' => 'Durasi kendaraan berhenti minimal 1 menit.',

            'stop_minutes.max' => 'Durasi kendaraan berhenti maksimal 1440 menit (24 jam).',

            'email_notification.required' => 'Status notifikasi email wajib dikirim.',

            'email_notification.boolean' => 'Status notifikasi email tidak valid.',

            'whatsapp_notification.required' => 'Status notifikasi WhatsApp wajib dikirim.',

            'whatsapp_notification.boolean' => 'Status notifikasi WhatsApp tidak valid.',

        ];
    }
}
