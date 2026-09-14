<?php

namespace App\Http\Requests;

use App\Services\Geofence\GeofenceEventService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGeofenceSettingRequest extends FormRequest
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

            'repeat_enabled' => [
                'required',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | Jeda pengingat dibatasi di bawah supaya kuota email/WhatsApp
            | tidak habis oleh pengingat yang terlalu rapat - interval lebih
            | pendek dari MIN_REPEAT_MINUTES tidak menambah informasi apa pun.
            |--------------------------------------------------------------------------
            */

            'repeat_minutes' => [
                'required',
                'integer',
                'min:' . GeofenceEventService::MIN_REPEAT_MINUTES,
                'max:' . GeofenceEventService::MAX_REPEAT_MINUTES,
            ],

        ];
    }

    /**
     * Validation Messages
     */
    public function messages(): array
    {
        return [

            'repeat_enabled.required' => 'Status pengingat wajib dikirim.',

            'repeat_enabled.boolean' => 'Status pengingat tidak valid.',

            'repeat_minutes.required' => 'Jeda pengingat wajib diisi.',

            'repeat_minutes.integer' => 'Jeda pengingat harus berupa angka menit.',

            'repeat_minutes.min' => 'Jeda pengingat minimal '
                . GeofenceEventService::MIN_REPEAT_MINUTES . ' menit.',

            'repeat_minutes.max' => 'Jeda pengingat maksimal '
                . GeofenceEventService::MAX_REPEAT_MINUTES . ' menit.',

        ];
    }
}
