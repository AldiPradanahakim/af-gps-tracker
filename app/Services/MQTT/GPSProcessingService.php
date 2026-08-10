<?php

namespace App\Services\MQTT;

use App\Helpers\GpsTimestampParser;
use App\Jobs\ResolveGpsAddressJob;
use App\Models\Device;
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
use App\Services\MQTT\RealtimeService;

class GPSProcessingService
{
    /**
     * Placeholder search_address saat cache reverse-geocoding miss di
     * jalur sinkron - diganti alamat asli oleh ResolveGpsAddressJob.
     */
    private const ADDRESS_PENDING_PLACEHOLDER = 'Memuat alamat...';

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
        | Verify Signature
        |--------------------------------------------------------------------------
        |
        | Mencegah device_id spoofing: siapa pun yang tahu kredensial broker
        | MQTT bisa mencantumkan device_id milik orang lain di payload.
        | Signature HMAC per-device memastikan payload benar-benar berasal
        | dari device yang mengetahui mqtt_secret-nya. Bisa dimatikan
        | sementara lewat MQTT_REQUIRE_SIGNATURE=false saat firmware belum
        | mendukung signing (mis. tahap awal bring-up perangkat).
        |--------------------------------------------------------------------------
        */

        if (config('mqtt.require_signature')) {

            $this->signatureService->verify(
                $device,
                $payload
            );
        }

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
        |
        | Jika device sebelumnya sempat ditandai offline (lihat
        | DeviceHealthCheckCommand), heartbeat baru ini berarti dia
        | baru saja kembali online - kirim notifikasi "online kembali"
        | tepat di titik transisinya, bukan menunggu job berkala
        | berikutnya berjalan.
        |--------------------------------------------------------------------------
        */

        $wasOfflineNotified = $device->offline_notified_at !== null;

        $this->deviceLookupService
            ->updateHeartbeat(

                $device,

                $this->extractReceivedAt(
                    $payload
                )

            );

        if ($wasOfflineNotified) {

            $this->notificationService
                ->createDeviceOnlineNotification(
                    $device
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Update Battery + Low Battery Detection
        |--------------------------------------------------------------------------
        |
        | Battery didenormalisasi ke devices.last_battery (dipakai juga
        | oleh tampilan status device). Deteksi transisi normal ->
        | baterai lemah dilakukan sinkron di sini (bukan job berkala)
        | karena nilainya sudah tersedia di setiap payload yang masuk.
        |--------------------------------------------------------------------------
        */

        $battery = (int) $payload['battery'];

        $this->deviceLookupService
            ->updateBattery(

                $device,

                $battery

            );

        $lowBatteryThreshold = (int) config('mqtt.alerts.low_battery_threshold');

        $isLowBattery = $battery <= $lowBatteryThreshold;

        $wasLowBattery = (bool) $device->low_battery_active;

        if ($isLowBattery && ! $wasLowBattery) {

            $device->update([
                'low_battery_active' => true,
            ]);

            $this->notificationService
                ->createLowBatteryNotification(

                    $device,

                    $battery

                );

        } elseif (! $isLowBattery && $wasLowBattery) {

            $device->update([
                'low_battery_active' => false,
            ]);
        }

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
        | Reverse Geocoding (Cache-Only, Non-Blocking)
        |--------------------------------------------------------------------------
        |
        | HANYA cek cache di jalur sinkron ini - memanggil provider
        | reverse-geocoding secara sinkron (search()) berarti pipeline
        | ingest MQTT (satu proses, satu pesan diproses per waktu) ikut
        | menunggu throttle rate limit global provider (600ms-1,1 detik
        | per panggilan), yang secara langsung melanggar target NF-01
        | (update posisi <=3 detik) begitu ada lebih dari beberapa
        | device/menit dengan koordinat baru. Kalau belum ada di cache,
        | pakai placeholder dan selesaikan alamat sesungguhnya secara
        | asinkron lewat ResolveGpsAddressJob (lihat di bawah).
        |--------------------------------------------------------------------------
        */

        $searchAddress =

            $this->reverseGeocodingService
            ->searchCached(

                (float) $payload['lat'],

                (float) $payload['lng']

            );

        $addressPending = $searchAddress === null;

        $searchAddress ??= self::ADDRESS_PENDING_PLACEHOLDER;

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
        | Resolve Address Asynchronously
        |--------------------------------------------------------------------------
        |
        | Cache miss di atas - selesaikan alamat sesungguhnya di queue
        | worker (boleh menunggu throttle provider) lalu update baris
        | travel_history ini setelahnya. Tidak memblokir proses ingest.
        |--------------------------------------------------------------------------
        */

        if ($addressPending) {

            ResolveGpsAddressJob::dispatch(

                latitude: (float) $payload['lat'],

                longitude: (float) $payload['lng'],

                travelHistoryId: $travelHistory->id,

            );
        }

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
        | Overspeed Detection
        |--------------------------------------------------------------------------
        |
        | Notifikasi hanya dikirim saat transisi normal -> overspeed
        | (lihat OverspeedDetectionService), bukan setiap titik selama
        | kendaraan masih di atas batas.
        |--------------------------------------------------------------------------
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

                        'lat' => $payload['lat'],

                        'lng' => $payload['lng'],

                        'speed' => $payload['speed'],

                        'search_address' => $searchAddress,

                    ]

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
        | Geofence Enter / Exit Notification
        |--------------------------------------------------------------------------
        |
        | Notification hanya dibuat ketika status berubah
        | (INSIDE <-> OUTSIDE), bukan setiap posisi GPS baru.
        |
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

        if ($geofenceResult['entered']) {

            $this->notificationService
                ->createGeofenceEnterNotification(

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

        return GpsTimestampParser::parse(
            $payload['received_at']
        );
    }
}
