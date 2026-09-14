<?php

namespace App\Services\Device;

use App\Models\Device;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

class DeviceLookupService
{
    /**
     * Find active device by device_id.
     *
     * @throws InvalidArgumentException
     * @throws ModelNotFoundException
     */
    public function findByDeviceId(
        string $deviceId
    ): Device {

        $deviceId = trim($deviceId);

        if ($deviceId === '') {

            throw new InvalidArgumentException(
                'ID Perangkat wajib diisi.'
            );
        }

        $device = Device::query()

            ->where(
                'device_id',
                $deviceId
            )

            ->first();

        if (! $device) {

            throw new ModelNotFoundException(
                sprintf(
                    'Device [%s] not found.',
                    $deviceId
                )
            );
        }

        return $device;
    }

    /**
     * Find device from MQTT payload.
     *
     * @throws InvalidArgumentException
     * @throws ModelNotFoundException
     */
    public function findFromPayload(
        array $payload
    ): Device {

        if (! isset($payload['device_id'])) {

            throw new InvalidArgumentException(
                'device_id is required.'
            );
        }

        return $this->findByDeviceId(
            $payload['device_id']
        );
    }

    /**
     * Determine whether device exists.
     */
    public function exists(
        string $deviceId
    ): bool {

        return Device::query()

            ->where(
                'device_id',
                trim($deviceId)
            )

            ->exists();
    }

    /**
     * Determine whether device is active.
     */
    public function isActive(
        Device $device
    ): bool {

        return (bool) $device->is_active;
    }

    /**
     * Ensure device is activated.
     *
     * @throws InvalidArgumentException
     */
    public function ensureActivated(
        Device $device
    ): Device {

        if (! $device->is_active) {

            throw new InvalidArgumentException(
                sprintf(
                    'Device [%s] is not activated.',
                    $device->device_id
                )
            );
        }

        return $device;
    }

    /**
     * Update device heartbeat.
     */
    public function updateHeartbeat(
        Device $device,
        $receivedAt
    ): void {

        $device->update([

            'last_heartbeat' => $receivedAt,

            /*
            |--------------------------------------------------------------------------
            | Reset Offline Notification State
            |--------------------------------------------------------------------------
            |
            | Heartbeat baru masuk = device sudah online lagi. Reset
            | penanda supaya episode offline berikutnya bisa dinotifikasi
            | lagi (lihat DeviceHealthCheckCommand).
            |--------------------------------------------------------------------------
            */

            'offline_notified_at' => null,

        ]);
    }

    /**
     * Update battery level terakhir (didenormalisasi dari payload GPS).
     */
    public function updateBattery(
        Device $device,
        int $battery
    ): void {

        $device->update([

            'last_battery' => $battery,

        ]);
    }

    /**
     * Load relations commonly used
     * during GPS processing.
     */
    public function loadRelations(
        Device $device
    ): Device {

        return $device->load([

            'vehicle',

            'geofences',

        ]);
    }
}
