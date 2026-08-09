<?php

namespace App\Services\MQTT;

use App\Models\Device;
use App\Services\Device\DeviceLookupService;
use App\Services\Device\DeviceLogService;
use App\Services\Device\TravelHistoryService;
use App\Services\Device\StopDetectionService;
use App\Services\Geofence\GeofenceCheckerService;
use App\Services\Geofence\ReverseGeocodingService;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use App\Services\MQTT\RealtimeService;

class GPSProcessingService
{
    public function __construct(

        protected DeviceLookupService $deviceLookupService,

        protected DeviceLogService $deviceLogService,

        protected TravelHistoryService $travelHistoryService,

        protected StopDetectionService $stopDetectionService,

        protected ReverseGeocodingService $reverseGeocodingService,

        protected GeofenceCheckerService $geofenceCheckerService,

        protected NotificationService $notificationService,

        protected RealtimeService $realtimeService,

    ) {}

    /**
     * Process incoming GPS payload.
     */
    public function process(
        array $payload
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Validate Payload
        |--------------------------------------------------------------------------
        */

        $this->validatePayload(
            $payload
        );

        /*
        |--------------------------------------------------------------------------
        | Find Device
        |--------------------------------------------------------------------------
        */

        $device =

            $this->deviceLookupService
            ->findFromPayload(
                $payload
            );

        /*
        |--------------------------------------------------------------------------
        | Ensure Activated
        |--------------------------------------------------------------------------
        */

        $device =

            $this->deviceLookupService
            ->ensureActivated(
                $device
            );

        /*
        |--------------------------------------------------------------------------
        | Load Required Relations
        |--------------------------------------------------------------------------
        */

        $device =

            $this->deviceLookupService
            ->loadRelations(
                $device
            );

        /*
        |--------------------------------------------------------------------------
        | Update Heartbeat
        |--------------------------------------------------------------------------
        */

        $this->deviceLookupService
            ->updateHeartbeat(

                $device,

                $this->extractReceivedAt(
                    $payload
                )

            );

        /*
        |--------------------------------------------------------------------------
        | Store Device Log
        |--------------------------------------------------------------------------
        |
        | Menyimpan payload asli MQTT.
        | Menghindari duplikasi berdasarkan message_id.
        |
        */

        $isDuplicateMessage =

            $this->deviceLogService
            ->isDuplicate(

                $device,

                $payload

            );

        $deviceLog =

            $this->deviceLogService
            ->store(

                $device,

                $payload

            );

        /*
        |--------------------------------------------------------------------------
        | Skip Duplicate Message
        |--------------------------------------------------------------------------
        |
        | Pesan MQTT dengan message_id yang sama (retry/replay dari
        | broker) tidak boleh diproses ulang: reverse geocoding, travel
        | history, stop detection, geofence check, notifikasi, dan
        | broadcast realtime semuanya hanya untuk pesan baru.
        |
        */

        if ($isDuplicateMessage) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Reverse Geocoding
        |--------------------------------------------------------------------------
        |
        | Mengubah koordinat GPS menjadi alamat.
        |
        */

        $searchAddress =

            $this->reverseGeocodingService
            ->search(

                (float) $payload['lat'],

                (float) $payload['lng']

            );

        /*
        |--------------------------------------------------------------------------
        | Store Travel History
        |--------------------------------------------------------------------------
        |
        | Menyimpan histori perjalanan kendaraan.
        |
        */

        $travelHistory =

            $this->travelHistoryService
            ->store(

                device: $device,

                deviceLog: $deviceLog,

                payload: $payload,

                searchAddress: $searchAddress

            );

        /*
        |--------------------------------------------------------------------------
        | Stop Detection
        |--------------------------------------------------------------------------
        |
        | Akan mengembalikan StopHistory apabila:
        | - Stop Detection aktif
        | - Kendaraan berhenti
        | - Durasi minimal tercapai
        | - Notification belum pernah dikirim
        |
        */

        $stopHistory =

            $this->stopDetectionService
            ->process(

                device: $device,

                payload: $payload,

                searchAddress: $searchAddress

            );

        /*
        |--------------------------------------------------------------------------
        | Stop Notification
        |--------------------------------------------------------------------------
        */

        if ($stopHistory) {

            $this->notificationService
                ->createStopNotification(

                    $device,

                    $stopHistory

                );

            $this->stopDetectionService
                ->markNotificationSent(

                    $stopHistory

                );
        }

        /*
        |--------------------------------------------------------------------------
        | Geofence Checking
        |--------------------------------------------------------------------------
        */

        $geofenceResult =

            $this->geofenceCheckerService
            ->process(

                $device,

                $payload

            );

        /*
        |--------------------------------------------------------------------------
        | Geofence Exit Notification
        |--------------------------------------------------------------------------
        */

        if ($geofenceResult['exited']) {

            $this->notificationService
                ->createGeofenceExitNotification(

                    device: $device,

                    geofence: $geofenceResult['geofence'],

                    payload: [

                        'lat' => $payload['lat'],

                        'lng' => $payload['lng'],

                        'search_address' => $searchAddress,

                    ]

                );
        }

        /*
        |--------------------------------------------------------------------------
        | Realtime Broadcast
        |--------------------------------------------------------------------------
        |
        | Broadcast lokasi terbaru kendaraan ke frontend
        | menggunakan Laravel Broadcasting.
        |
        */

        $this->realtimeService
            ->broadcast(

                device: $device,

                travelHistory: $travelHistory,

                geofenceResult: $geofenceResult

            );
    }

    /**
     * Validate MQTT payload.
     *
     * @throws InvalidArgumentException
     */
    protected function validatePayload(
        array $payload
    ): void {

        $required = [

            'device_id',

            'message_id',

            'lat',

            'lng',

            'speed',

            'heading',

            'battery',

            'satellite',

            'received_at',

        ];

        foreach ($required as $field) {

            if (! array_key_exists(
                $field,
                $payload
            )) {

                throw new InvalidArgumentException(

                    sprintf(
                        '%s is required.',
                        $field
                    )

                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Bounds Check
        |--------------------------------------------------------------------------
        |
        | Payload GPS berasal dari perangkat fisik (atau siapa pun yang
        | bisa publish ke broker MQTT dengan kredensial yang valid), jadi
        | nilainya tidak bisa dipercaya begitu saja sebelum disimpan dan
        | di-broadcast ke live map.
        |
        */

        $numericFields = [
            'lat', 'lng', 'speed', 'heading', 'battery', 'satellite',
        ];

        foreach ($numericFields as $field) {

            if (! is_numeric($payload[$field])) {

                throw new InvalidArgumentException(

                    sprintf('%s must be numeric.', $field)

                );
            }
        }

        $lat = (float) $payload['lat'];
        $lng = (float) $payload['lng'];
        $speed = (float) $payload['speed'];
        $heading = (float) $payload['heading'];
        $battery = (float) $payload['battery'];
        $satellite = (float) $payload['satellite'];

        if ($lat < -90 || $lat > 90) {
            throw new InvalidArgumentException('lat is out of range.');
        }

        if ($lng < -180 || $lng > 180) {
            throw new InvalidArgumentException('lng is out of range.');
        }

        if ($speed < 0 || $speed > 300) {
            throw new InvalidArgumentException('speed is out of range.');
        }

        if ($heading < 0 || $heading > 360) {
            throw new InvalidArgumentException('heading is out of range.');
        }

        if ($battery < 0 || $battery > 100) {
            throw new InvalidArgumentException('battery is out of range.');
        }

        if ($satellite < 0 || $satellite > 50) {
            throw new InvalidArgumentException('satellite is out of range.');
        }

        try {
            $receivedAt = $this->extractReceivedAt($payload);
        } catch (\Throwable) {
            throw new InvalidArgumentException('received_at is invalid.');
        }

        if ($receivedAt->lessThan(now()->subDay()) || $receivedAt->greaterThan(now()->addMinutes(5))) {

            throw new InvalidArgumentException('received_at is outside the acceptable time window.');

        }
    }

    /**
     * Convert payload timestamp into Carbon.
     */
    protected function extractReceivedAt(
        array $payload
    ): Carbon {

        return Carbon::parse(
            $payload['received_at']
        );
    }
}
