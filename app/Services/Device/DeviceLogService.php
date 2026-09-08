<?php

namespace App\Services\Device;

use App\Helpers\GpsTimestampParser;
use App\Models\Device;
use App\Models\DeviceLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DeviceLogService
{
    /**
     * Cek apakah message_id ini sudah pernah tersimpan
     * untuk device tersebut.
     *
     * Digunakan untuk mencegah duplicate MQTT message.
     */
    public function isDuplicate(
        Device $device,
        array $payload
    ): bool {

        $messageId = $this->extractMessageId(
            $payload
        );

        return DeviceLog::query()
            ->where(
                'device_id',
                $device->id
            )
            ->where(
                'message_id',
                $messageId
            )
            ->exists();
    }

    /**
     * Store incoming device payload hanya jika:
     *
     * 1. Belum pernah memiliki message_id yang sama.
     * 2. Payload pertama untuk device.
     * 3. Jarak dari posisi terakhir >= threshold.
     *
     * Return:
     *
     * DeviceLog -> jika posisi harus disimpan.
     * null      -> jika posisi tidak memenuhi threshold.
     */
    public function store(
        Device $device,
        array $payload
    ): ?DeviceLog {

        return DB::transaction(
            function () use (
                $device,
                $payload
            ) {

                /*
                |--------------------------------------------------------------------------
                | Extract Data
                |--------------------------------------------------------------------------
                */

                $messageId = $this->extractMessageId(
                    $payload
                );

                $receivedAt = $this->extractReceivedAt(
                    $payload
                );

                /*
                |--------------------------------------------------------------------------
                | Duplicate Message
                |--------------------------------------------------------------------------
                */

                $existing = DeviceLog::query()
                    ->where(
                        'device_id',
                        $device->id
                    )
                    ->where(
                        'message_id',
                        $messageId
                    )
                    ->first();

                if ($existing) {
                    return $existing;
                }

                /*
                |--------------------------------------------------------------------------
                | Check Position Movement & Speed Condition
                |--------------------------------------------------------------------------
                |
                | Aturan Persistence:
                |
                | 1. speed < 1 km/h:
                |    Threshold 0,5 meter AKTIF.
                |    Jarak < 0,5 m  -> return null (jangan simpan DeviceLog)
                |    Jarak >= 0,5 m -> lanjut simpan DeviceLog
                |
                | 2. speed >= 1 km/h:
                |    Threshold 0,5 meter TIDAK AKTIF.
                |    Setiap payload GPS valid dan bukan duplicate langsung disimpan.
                |
                */

                $speed = (float) ($payload['speed'] ?? 0);

                if ($speed < 1.0) {

                    $lastLog = $this->getLastPositionLog(
                        $device
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | First Position
                    |--------------------------------------------------------------------------
                    |
                    | Kalau device belum pernah mempunyai DeviceLog,
                    | posisi pertama wajib disimpan sebagai reference point.
                    |
                    */

                    if ($lastLog !== null) {

                        $distance = $this->distanceFromLastPosition(
                            $lastLog,
                            $payload
                        );

                        $threshold = (float) config(
                            'mqtt.position_threshold',
                            0.5
                        );

                        /*
                        |--------------------------------------------------------------------------
                        | Position Below Threshold (< 0.5 m)
                        |--------------------------------------------------------------------------
                        |
                        | Ketika speed < 1 km/h dan distance < 0.5 m,
                        | maka payload TIDAK dibuatkan DeviceLog.
                        |
                        */

                        if ($distance < $threshold) {

                            return null;
                        }
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Store Device Log
                |--------------------------------------------------------------------------
                */

                return DeviceLog::create([

                    'device_id' => $device->id,

                    'message_id' => $messageId,

                    'payload' => $payload,

                    'status' => 'valid',

                    'received_at' => $receivedAt,

                ]);
            }
        );
    }

    /**
     * Get latest DeviceLog containing the last accepted GPS position.
     */
    protected function getLastPositionLog(
        Device $device
    ): ?DeviceLog {

        return DeviceLog::query()

            ->where(
                'device_id',
                $device->id
            )

            ->latest(
                'received_at'
            )

            ->first();
    }

    /**
     * Calculate distance between the last accepted position
     * and the incoming GPS position.
     *
     * Return value is in meters.
     */
    public function distanceFromLastPosition(
        DeviceLog $lastLog,
        array $payload
    ): float {

        $lastPayload = $lastLog->payload ?? [];

        /*
        |--------------------------------------------------------------------------
        | Last Latitude
        |--------------------------------------------------------------------------
        */

        $lastLat = $this->extractLatitude(
            $lastPayload
        );

        /*
        |--------------------------------------------------------------------------
        | Last Longitude
        |--------------------------------------------------------------------------
        */

        $lastLng = $this->extractLongitude(
            $lastPayload
        );

        /*
        |--------------------------------------------------------------------------
        | Current Latitude
        |--------------------------------------------------------------------------
        */

        $currentLat = (float) $payload['lat'];

        /*
        |--------------------------------------------------------------------------
        | Current Longitude
        |--------------------------------------------------------------------------
        */

        $currentLng = (float) $payload['lng'];

        return $this->haversineDistance(

            $lastLat,

            $lastLng,

            $currentLat,

            $currentLng

        );
    }

    /**
     * Haversine distance calculation.
     *
     * Return:
     * distance in meters.
     */
    public function haversineDistance(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2
    ): float {

        /*
        |--------------------------------------------------------------------------
        | Earth Radius
        |--------------------------------------------------------------------------
        |
        | Average Earth radius in meters.
        |
        */

        $earthRadius = 6371000;

        /*
        |--------------------------------------------------------------------------
        | Convert Degrees -> Radians
        |--------------------------------------------------------------------------
        */

        $lat1Rad = deg2rad(
            $lat1
        );

        $lat2Rad = deg2rad(
            $lat2
        );

        $deltaLat = deg2rad(
            $lat2 - $lat1
        );

        $deltaLng = deg2rad(
            $lng2 - $lng1
        );

        /*
        |--------------------------------------------------------------------------
        | Haversine Formula
        |--------------------------------------------------------------------------
        */

        $a =

            sin($deltaLat / 2) ** 2

            +

            cos($lat1Rad)
            *
            cos($lat2Rad)
            *
            sin($deltaLng / 2) ** 2;

        $c = 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );

        return $earthRadius * $c;
    }

    /**
     * Extract latitude from stored payload.
     */
    protected function extractLatitude(
        array $payload
    ): float {

        if (isset($payload['lat'])) {

            return (float) $payload['lat'];
        }

        if (isset($payload['latitude'])) {

            return (float) $payload['latitude'];
        }

        throw new InvalidArgumentException(
            'Last DeviceLog payload does not contain latitude.'
        );
    }

    /**
     * Extract longitude from stored payload.
     */
    protected function extractLongitude(
        array $payload
    ): float {

        if (isset($payload['lng'])) {

            return (float) $payload['lng'];
        }

        if (isset($payload['longitude'])) {

            return (float) $payload['longitude'];
        }

        throw new InvalidArgumentException(
            'Last DeviceLog payload does not contain longitude.'
        );
    }

    /**
     * Extract message ID.
     */
    protected function extractMessageId(
        array $payload
    ): int {

        if (! isset($payload['message_id'])) {

            throw new InvalidArgumentException(
                'message_id is required.'
            );
        }

        if (! is_numeric(
            $payload['message_id']
        )) {

            throw new InvalidArgumentException(
                'Invalid message_id.'
            );
        }

        return (int) $payload['message_id'];
    }

    /**
     * Extract received_at.
     */
    protected function extractReceivedAt(
        array $payload
    ): Carbon {

        if (! isset($payload['received_at'])) {

            throw new InvalidArgumentException(
                'received_at is required.'
            );
        }

        return GpsTimestampParser::parse(
            $payload['received_at']
        );
    }
}