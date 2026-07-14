<?php

namespace App\Services;

use App\Models\Device;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class DeviceService
{
    public function activateDevice(User $user, array $data): Device
    {
        $device = Device::where('device_id', $data['device_id'])->first();

        if (! $device) {
            throw ValidationException::withMessages([
                'device_id' => __('Device ID tidak ditemukan.'),
            ]);
        }

        if ($device->device_password !== $data['device_password']) {
            throw ValidationException::withMessages([
                'device_password' => __('Password device salah.'),
            ]);
        }

        if ($device->user_id && $device->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'device_id' => __('Device ini sudah dimiliki oleh user lain.'),
            ]);
        }

        $device->update([
            'user_id' => $user->id,
            'activated_at' => now(),
            'is_active' => true,
        ]);

        return $device;
    }
}
