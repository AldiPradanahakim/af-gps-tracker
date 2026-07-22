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

        $deviceLog =

            $this->deviceLogService
            ->store(

                $device,

                $payload

            );

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
