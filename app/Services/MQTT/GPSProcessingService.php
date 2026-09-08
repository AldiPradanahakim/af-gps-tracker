<?php

namespace App\Services\MQTT;

use App\Events\VehicleLocationUpdated;
use App\Helpers\GpsTimestampParser;
use App\Jobs\ResolveGpsAddressJob;
use App\Models\Device;
use App\Models\TravelHistory;
use App\Services\Device\DeviceLookupService;
use App\Services\Device\DeviceLogService;
use App\Services\Device\TravelHistoryService;
use App\Services\Device\StopDetectionService;
use App\Services\Device\OverspeedDetectionService;
use App\Services\Geofence\GeofenceCheckerService;
use App\Services\Geofence\ReverseGeocodingService;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

class GPSProcessingService
{
    /**
     * Placeholder search_address saat cache reverse-geocoding miss.
     */
    private const ADDRESS_PENDING_PLACEHOLDER =
        'Memuat alamat...';

    public function __construct(

        protected DeviceLookupService $deviceLookupService,

        protected DeviceLogService $deviceLogService,

        protected TravelHistoryService $travelHistoryService,

        protected StopDetectionService $stopDetectionService,

        protected OverspeedDetectionService $overspeedDetectionService,

        protected ReverseGeocodingService $reverseGeocodingService,

        protected GeofenceCheckerService $geofenceCheckerService,

        protected NotificationService $notificationService,

        protected RealtimeService $realtimeService,

        protected MQTTSignatureService $signatureService,

    ) {}

    /**
     * Process incoming GPS payload.
     */
    public function process(
        array $payload
    ): void {

        /*
        |--------------------------------------------------------------------------
        | 1. Validate Payload
        |--------------------------------------------------------------------------
        */

        $this->validatePayload(
            $payload
        );

        /*
        |--------------------------------------------------------------------------
        | 2. Find Device
        |--------------------------------------------------------------------------
        */

        $device =
            $this->deviceLookupService
                ->findFromPayload(
                    $payload
                );

        /*
        |--------------------------------------------------------------------------
        | 3. Ensure Activated
        |--------------------------------------------------------------------------
        */

        $device =
            $this->deviceLookupService
                ->ensureActivated(
                    $device
                );

        /*
        |--------------------------------------------------------------------------
        | 4. Verify Signature
        |--------------------------------------------------------------------------
        */

        if (
            config(
                'mqtt.require_signature'
            )
        ) {

            $this->signatureService->verify(
                $device,
                $payload
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Load Relations
        |--------------------------------------------------------------------------
        */

        $device =
            $this->deviceLookupService
                ->loadRelations(
                    $device
                );

        /*
        |--------------------------------------------------------------------------
        | 6. Heartbeat
        |--------------------------------------------------------------------------
        */

        $wasOfflineNotified =
            $device->offline_notified_at !== null;

        $receivedAt =
            $this->extractReceivedAt(
                $payload
            );

        $this->deviceLookupService
            ->updateHeartbeat(
                $device,
                $receivedAt
            );

        /*
        |--------------------------------------------------------------------------
        | Device Online Notification
        |--------------------------------------------------------------------------
        */

        if ($wasOfflineNotified) {

            $this->notificationService
                ->createDeviceOnlineNotification(
                    $device
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Battery
        |--------------------------------------------------------------------------
        */

        $battery = (int) $payload['battery'];

        $this->deviceLookupService
            ->updateBattery(
                $device,
                $battery
            );

        /*
        |--------------------------------------------------------------------------
        | Low Battery Detection
        |--------------------------------------------------------------------------
        */

        $lowBatteryThreshold =
            (int) config(
                'mqtt.alerts.low_battery_threshold'
            );

        $isLowBattery =
            $battery <= $lowBatteryThreshold;

        $wasLowBattery =
            (bool) $device->low_battery_active;

        if (
            $isLowBattery
            &&
            ! $wasLowBattery
        ) {

            $device->update([
                'low_battery_active' => true,
            ]);

            $this->notificationService
                ->createLowBatteryNotification(
                    $device,
                    $battery
                );

        } elseif (
            ! $isLowBattery
            &&
            $wasLowBattery
        ) {

            $device->update([
                'low_battery_active' => false,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 8. Duplicate Message Check
        |--------------------------------------------------------------------------
        |
        | Duplicate message tidak boleh diproses ulang.
        |
        | Heartbeat dan battery sudah diproses sebelumnya.
        |
        */

        $isDuplicateMessage =
            $this->deviceLogService
                ->isDuplicate(
                    $device,
                    $payload
                );

        if ($isDuplicateMessage) {

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 9. Geofence
        |--------------------------------------------------------------------------
        |
        | Geofence dihitung SEBELUM realtime supaya payload realtime
        | tetap membawa status geofence terbaru.
        |
        */

        $geofenceResult =
            $this->geofenceCheckerService
                ->process(
                    $device,
                    $payload
                );

        /*
        |--------------------------------------------------------------------------
        | 10. REALTIME BROADCAST
        |--------------------------------------------------------------------------
        |
        | SANGAT PENTING:
        |
        | Realtime dilakukan SEBELUM:
        |
        | - DeviceLog persistence
        | - TravelHistory
        | - Reverse geocoding
        | - Stop detection
        | - Overspeed notification
        | - Geofence notification
        |
        | VehicleLocationUpdated menggunakan ShouldBroadcastNow,
        | sehingga tidak menunggu queue worker.
        |
        */

        $this->realtimeService
            ->broadcast(
                device: $device,

                payload: $payload,

                geofenceResult:
                    $geofenceResult,

                travelHistory: null,

                searchAddress: null
            );

        /*
        |--------------------------------------------------------------------------
        | 11. Reverse Geocoding Cache
        |--------------------------------------------------------------------------
        |
        | Setelah realtime dikirim, baru kita proses alamat.
        |
        */

        $searchAddress =
            $this->reverseGeocodingService
                ->searchCached(

                    (float) $payload['lat'],

                    (float) $payload['lng']

                );

        $addressPending =
            $searchAddress === null;

        $searchAddress ??=
            self::ADDRESS_PENDING_PLACEHOLDER;

        /*
        |--------------------------------------------------------------------------
        | 12. DeviceLog + Position Threshold
        |--------------------------------------------------------------------------
        |
        | DeviceLogService:
        |
        | distance < 0.5 m
        |     -> null
        |
        | distance >= 0.5 m
        |     -> insert DeviceLog
        |
        | Realtime TIDAK dipengaruhi threshold ini.
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
        | 13. Travel History
        |--------------------------------------------------------------------------
        |
        | TravelHistory hanya dibuat ketika DeviceLog dibuat.
        |
        */

        $travelHistory = null;

        if ($deviceLog !== null) {

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
            | Resolve Address Asynchronously
            |--------------------------------------------------------------------------
            */

            if ($addressPending) {

                ResolveGpsAddressJob::dispatch(

                    latitude:
                        (float) $payload['lat'],

                    longitude:
                        (float) $payload['lng'],

                    travelHistoryId:
                        $travelHistory->id,
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 14. Stop Detection
        |--------------------------------------------------------------------------
        |
        | Tetap setiap payload.
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
        | 15. Overspeed Detection
        |--------------------------------------------------------------------------
        |
        | Tetap setiap payload.
        |
        */

        $isOverspeedTransition =
            $this->overspeedDetectionService
                ->process(
                    $device,
                    $payload
                );

        if ($isOverspeedTransition) {

            $this->notificationService
                ->createOverspeedNotification(

                    $device,

                    [
                        'lat' =>
                            $payload['lat'],

                        'lng' =>
                            $payload['lng'],

                        'speed' =>
                            $payload['speed'],

                        'search_address' =>
                            $searchAddress,
                    ]
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 16. Geofence Exit Notification
        |--------------------------------------------------------------------------
        */

        if (
            $geofenceResult['exited']
            ?? false
        ) {

            $this->notificationService
                ->createGeofenceExitNotification(

                    device: $device,

                    geofence:
                        $geofenceResult['geofence'],

                    payload: [

                        'lat' =>
                            $payload['lat'],

                        'lng' =>
                            $payload['lng'],

                        'search_address' =>
                            $searchAddress,
                    ]
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 17. Geofence Enter Notification
        |--------------------------------------------------------------------------
        */

        if (
            $geofenceResult['entered']
            ?? false
        ) {

            $this->notificationService
                ->createGeofenceEnterNotification(

                    device: $device,

                    geofence:
                        $geofenceResult['geofence'],

                    payload: [

                        'lat' =>
                            $payload['lat'],

                        'lng' =>
                            $payload['lng'],

                        'search_address' =>
                            $searchAddress,
                    ]
                );
        }
    }

    /**
     * Validate MQTT payload.
     *
     * @throws InvalidArgumentException
     */
    protected function validatePayload(
        array $payload
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Required Fields
        |--------------------------------------------------------------------------
        */

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
        | Numeric Fields
        |--------------------------------------------------------------------------
        */

        $numericFields = [

            'lat',

            'lng',

            'speed',

            'heading',

            'battery',

            'satellite',
        ];

        foreach ($numericFields as $field) {

            if (! is_numeric(
                $payload[$field]
            )) {

                throw new InvalidArgumentException(

                    sprintf(
                        '%s must be numeric.',
                        $field
                    )
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Cast
        |--------------------------------------------------------------------------
        */

        $lat = (float) $payload['lat'];

        $lng = (float) $payload['lng'];

        $speed = (float) $payload['speed'];

        $heading = (float) $payload['heading'];

        $battery = (float) $payload['battery'];

        $satellite = (float) $payload['satellite'];

        /*
        |--------------------------------------------------------------------------
        | Latitude
        |--------------------------------------------------------------------------
        */

        if (
            $lat < -90
            ||
            $lat > 90
        ) {

            throw new InvalidArgumentException(
                'lat is out of range.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Longitude
        |--------------------------------------------------------------------------
        */

        if (
            $lng < -180
            ||
            $lng > 180
        ) {

            throw new InvalidArgumentException(
                'lng is out of range.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Speed
        |--------------------------------------------------------------------------
        */

        if (
            $speed < 0
            ||
            $speed > 300
        ) {

            throw new InvalidArgumentException(
                'speed is out of range.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Heading
        |--------------------------------------------------------------------------
        */

        if (
            $heading < 0
            ||
            $heading > 360
        ) {

            throw new InvalidArgumentException(
                'heading is out of range.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Battery
        |--------------------------------------------------------------------------
        */

        if (
            $battery < 0
            ||
            $battery > 100
        ) {

            throw new InvalidArgumentException(
                'battery is out of range.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Satellite
        |--------------------------------------------------------------------------
        */

        if (
            $satellite < 0
            ||
            $satellite > 50
        ) {

            throw new InvalidArgumentException(
                'satellite is out of range.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | received_at
        |--------------------------------------------------------------------------
        */

        try {

            $receivedAt =
                $this->extractReceivedAt(
                    $payload
                );

        } catch (\Throwable) {

            throw new InvalidArgumentException(
                'received_at is invalid.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Timestamp Window
        |--------------------------------------------------------------------------
        */

        if (
            $receivedAt->lessThan(
                now()->subDay()
            )
            ||
            $receivedAt->greaterThan(
                now()->addMinutes(5)
            )
        ) {

            throw new InvalidArgumentException(
                'received_at is outside the acceptable time window.'
            );
        }
    }

    /**
     * Convert payload timestamp into Carbon.
     */
    protected function extractReceivedAt(
        array $payload
    ): Carbon {

        return GpsTimestampParser::parse(
            $payload['received_at']
        );
    }
}