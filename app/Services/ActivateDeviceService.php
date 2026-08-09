<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ActivateDeviceService
{
    public function activateDevice(array $data): Device
    {
        $device = Device::where('device_id', $data['device_id'])->first();

        if (! $device) {
            throw ValidationException::withMessages([
                'device_id' => 'Device ID tidak ditemukan.',
            ]);
        }

        if (! Hash::check($data['device_password'], $device->device_password)) {
            throw ValidationException::withMessages([
                'device_password' => 'Password device salah.',
            ]);
        }

        if ($device->user_id !== null) {
            throw ValidationException::withMessages([
                'device_id' => 'Perangkat sudah diaktivasi oleh pengguna lain.',
            ]);
        }

        session([
            'activated_device_id' => $device->id,
        ]);

        return $device;
    }
}
