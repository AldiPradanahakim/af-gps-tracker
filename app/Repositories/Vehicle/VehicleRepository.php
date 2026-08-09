<?php

namespace App\Repositories\Vehicle;

use App\Models\Device;
use App\Models\StopHistory;
use App\Models\TravelHistory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class VehicleRepository
{
    /**
     * --------------------------------------------------------------------------
     * Batas Pengaman Query Riwayat
     * --------------------------------------------------------------------------
     * Device GPS bisa mengirim log setiap beberapa detik sehingga
     * travel_histories & stop_histories tidak punya batas atas ukuran.
     * Endpoint history()/playback()/stop() tidak mewajibkan rentang tanggal,
     * jadi tanpa batas ini request dengan rentang lebar (atau tanpa filter
     * sama sekali) bisa menarik jutaan baris ke memori sekaligus.
     * --------------------------------------------------------------------------
     */
    private const MAX_HISTORY_ROWS = 20000;

    /**
     * Kolom travel_histories yang benar-benar dipakai oleh response
     * (device_log_id, device_id, created_at, updated_at tidak dipakai).
     */
    private const TRAVEL_HISTORY_COLUMNS = [
        'id',
        'location',
        'search_address',
        'received_at',
    ];

    /**
     * Kolom stop_histories yang benar-benar dipakai oleh response.
     */
    private const STOP_HISTORY_COLUMNS = [
        'id',
        'location',
        'search_address',
        'start_time',
        'end_time',
        'duration_seconds',
    ];

    /**
     * --------------------------------------------------------------------------
     * Transform Latest Device Log
     * --------------------------------------------------------------------------
     */
    private function transformLatestLocation(
        ?array $payload,
        $receivedAt,
        ?string $address = null
    ): array {

        $payload ??= [];

        return [

            'lat' => isset($payload['lat'])
                ? (float) $payload['lat']
                : null,

            'lng' => isset($payload['lng'])
                ? (float) $payload['lng']
                : null,

            'speed' => (float) ($payload['speed'] ?? 0),

            'heading' => (float) ($payload['heading'] ?? 0),

            'battery' => isset($payload['battery'])
                ? (float) $payload['battery']
                : null,

            'satellite' => isset($payload['satellite'])
                ? (int) $payload['satellite']
                : null,

            'address' => $address,

            'received_at' => optional(
                $receivedAt
            )?->toDateTimeString(),

        ];
    }

    /**
     * --------------------------------------------------------------------------
     * Transform Travel History
     * --------------------------------------------------------------------------
     */
    private function transformTravelHistory(
        TravelHistory $history
    ): array {

        $location = $history->location ?? [];

        return [

            'id' => $history->id,

            'lat' => isset($location['lat'])
                ? (float) $location['lat']
                : null,

            'lng' => isset($location['lng'])
                ? (float) $location['lng']
                : null,

            'speed' => (float) ($location['speed'] ?? 0),

            'heading' => (float) ($location['heading'] ?? 0),

            'battery' => isset($location['battery'])
                ? (float) $location['battery']
                : null,

            'satellite' => isset($location['satellite'])
                ? (int) $location['satellite']
                : null,

            'address' => $history->search_address,

            'received_at' => optional(
                $history->received_at
            )?->toDateTimeString(),

        ];
    }

    /**
     * --------------------------------------------------------------------------
     * Transform Collection
     * --------------------------------------------------------------------------
     */
    private function transformTravelCollection(
        Collection $histories
    ): array {

        return $histories

            ->map(fn(
                TravelHistory $history
            ) => $this->transformTravelHistory(
                $history
            ))

            ->values()

            ->toArray();
    }

    /**
     * --------------------------------------------------------------------------
     * Haversine Formula (Meter)
     * --------------------------------------------------------------------------
     */
    private function haversineDistance(

        float $lat1,

        float $lng1,

        float $lat2,

        float $lng2

    ): float {

        $earthRadius = 6371000;

        $latFrom = deg2rad($lat1);

        $lonFrom = deg2rad($lng1);

        $latTo = deg2rad($lat2);

        $lonTo = deg2rad($lng2);

        $latDelta = $latTo - $latFrom;

        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(

            sqrt(

                pow(
                    sin($latDelta / 2),
                    2
                )

                    +

                    cos($latFrom)

                    *

                    cos($latTo)

                    *

                    pow(
                        sin($lonDelta / 2),
                        2
                    )

            )

        );

        return $earthRadius * $angle;
    }

    /**
     * --------------------------------------------------------------------------
     * Calculate Total Distance (KM)
     * --------------------------------------------------------------------------
     */
    private function calculateTotalDistance(
        Collection $histories
    ): float {

        if ($histories->count() < 2) {

            return 0;
        }

        $distance = 0;

        for (

            $i = 1;

            $i < $histories->count();

            $i++

        ) {

            $previous = $histories[$i - 1];

            $current = $histories[$i];

            $previousLocation = $previous->location ?? [];

            $currentLocation = $current->location ?? [];

            if (

                !isset($previousLocation['lat']) ||

                !isset($previousLocation['lng']) ||

                !isset($currentLocation['lat']) ||

                !isset($currentLocation['lng'])

            ) {

                continue;
            }

            $distance += $this->haversineDistance(

                (float) $previousLocation['lat'],

                (float) $previousLocation['lng'],

                (float) $currentLocation['lat'],

                (float) $currentLocation['lng']

            );
        }

        return round(

            $distance / 1000,

            2

        );
    }


    /**
     * --------------------------------------------------------------------------
     * Detail Vehicle
     * --------------------------------------------------------------------------
     */
    public function detail(
        Device $device,
    ): array {

        /*
        |--------------------------------------------------------------------------
        | Load Relation
        |--------------------------------------------------------------------------
        */

        $device->load([

            'vehicle',

            'geofences',

        ]);

        /*
        |--------------------------------------------------------------------------
        | Semua Kendaraan User
        |--------------------------------------------------------------------------
        */

        $vehicles = Device::query()

            ->with('vehicle')

            ->where(
                'user_id',
                Auth::id()
            )

            ->orderBy('id')

            ->get();

        /*
        |--------------------------------------------------------------------------
        | Latest Device Log
        |--------------------------------------------------------------------------
        */

        $latestLog = $device->deviceLogs()

            ->latest('received_at')

            ->first();

        $latestLocation = $this->transformLatestLocation(

            $latestLog?->payload,

            $latestLog?->received_at,

            null

        );

        /*
        |--------------------------------------------------------------------------
        | Home Location
        |--------------------------------------------------------------------------
        */

        $homeLocation = $device->home_location;

        /*
        |--------------------------------------------------------------------------
        | Geofence
        |--------------------------------------------------------------------------
        */

        $geofences = $device->geofences

            ->map(function ($geofence) {

                return [

                    'id' => $geofence->id,

                    'name' => $geofence->name,

                    'description' => $geofence->description,

                    'type' => $geofence->type,

                    'config' => $geofence->config,

                    'status' => $geofence->status,

                ];
            })

            ->values();

        /*
        |--------------------------------------------------------------------------
        | Geofence by Type
        |--------------------------------------------------------------------------
        */

        $radius = $device->geofences
            ->firstWhere('type', 'radius');

        $administrative = $device->geofences
            ->firstWhere('type', 'administrative');

        $polygon = $device->geofences
            ->firstWhere('type', 'custom');

        /*
        |--------------------------------------------------------------------------
        | Today Travel
        |--------------------------------------------------------------------------
        */

        $todayTravel = $this->transformTravelCollection(

            $device->travelHistories()

                ->whereDate(
                    'received_at',
                    today()
                )

                ->orderBy('received_at')

                ->get()

        );

        /*
        |--------------------------------------------------------------------------
        | Playback History
        |--------------------------------------------------------------------------
        */

        $travelHistories = $this->transformTravelCollection(

            $device->travelHistories()

                ->orderBy('received_at')

                ->get()

        );

        /*
        |--------------------------------------------------------------------------
        | Stop Detection
        |--------------------------------------------------------------------------
        */

        $stopDetection = $this->buildStopDetection($device);

        $stopSummary = $this->buildStopSummary($device);

        /*
        |--------------------------------------------------------------------------
        | Return
        |--------------------------------------------------------------------------
        */

        return [

            'device' => $device,

            'vehicles' => $vehicles,

            'latestLocation' => $latestLocation,

            'homeLocation' => $homeLocation,

            'geofences' => $geofences,

            /*
            |--------------------------------------------------------------------------
            | Per Type
            |--------------------------------------------------------------------------
            */

            'radius' => $radius,

            'administrative' => $administrative,

            'polygon' => $polygon,

            'todayTravel' => $todayTravel,

            'travelHistories' => $travelHistories,

            'stopDetection' => $stopDetection,

            'stopSummary' => $stopSummary,

            'notificationSetting' => $this->buildNotificationSetting($device),

        ];
    }

    /**
     * --------------------------------------------------------------------------
     * Bangun objek pengaturan Notifikasi Geofence dari
     * device.notification_setting (JSON).
     * --------------------------------------------------------------------------
     */
    protected function buildNotificationSetting(Device $device): object
    {
        $setting = $device->notification_setting ?? [];

        return (object) [

            'email_notification' => (bool) ($setting['email'] ?? false),

            'whatsapp_notification' => (bool) ($setting['whatsapp'] ?? false),

        ];
    }

    /**
     * --------------------------------------------------------------------------
     * Bangun objek pengaturan Stop Detection dari device.stop_setting (JSON).
     * --------------------------------------------------------------------------
     */
    protected function buildStopDetection(Device $device): object
    {
        $setting = $device->stop_setting ?? [];

        return (object) [

            'enabled' => (bool) ($setting['enabled'] ?? false),

            'stop_minutes' => (int) ($setting['minutes'] ?? 5),

            'system_notification' => true,

            'email_notification' => (bool) ($setting['email_notification'] ?? false),

            'whatsapp_notification' => (bool) ($setting['whatsapp_notification'] ?? false),

        ];
    }

    /**
     * --------------------------------------------------------------------------
     * Ringkasan Stop Detection (total, hari ini, durasi terlama, total durasi).
     * --------------------------------------------------------------------------
     */
    protected function buildStopSummary(Device $device): array
    {
        $stops = $device->stopHistories()->get();

        $today = $stops->filter(

            fn(StopHistory $stop) => $stop->start_time?->isToday()

        );

        $longestSeconds = (int) $stops->max('duration_seconds');

        $totalSeconds = (int) $stops->sum('duration_seconds');

        return [

            'total_stop' => $stops->count(),

            'today_stop' => $today->count(),

            'longest_stop' => $this->formatDuration($longestSeconds),

            'total_duration' => $this->formatDuration($totalSeconds),

        ];
    }

    /**
     * --------------------------------------------------------------------------
     * Format detik -> "Xj Ym" / "Ym".
     * --------------------------------------------------------------------------
     */
    protected function formatDuration(int $seconds): string
    {
        if ($seconds <= 0) {
            return '-';
        }

        $hours = intdiv($seconds, 3600);

        $minutes = intdiv($seconds % 3600, 60);

        if ($hours > 0) {
            return "{$hours}j {$minutes}m";
        }

        return "{$minutes}m";
    }

    /**
     * --------------------------------------------------------------------------
     * Update Stop Detection Setting
     * --------------------------------------------------------------------------
     */
    public function updateStopSetting(
        Device $device,
        array $data
    ): object {

        $device->update([

            'stop_setting' => [

                'enabled' => (bool) $data['enabled'],

                'minutes' => (int) $data['stop_minutes'],

                'email_notification' => (bool) $data['email_notification'],

                'whatsapp_notification' => (bool) $data['whatsapp_notification'],

            ],

        ]);

        return $this->buildStopDetection(
            $device->fresh()
        );
    }

    /**
     * --------------------------------------------------------------------------
     * Update Notifikasi Geofence Setting
     * --------------------------------------------------------------------------
     */
    public function updateNotificationSetting(
        Device $device,
        array $data
    ): object {

        $device->update([

            'notification_setting' => [

                'email' => (bool) $data['email_notification'],

                'whatsapp' => (bool) $data['whatsapp_notification'],

            ],

        ]);

        return $this->buildNotificationSetting(
            $device->fresh()
        );
    }

    /**
     * --------------------------------------------------------------------------
     * Hapus Kendaraan (Device)
     * --------------------------------------------------------------------------
     * Vehicle, DeviceLog, TravelHistory, StopHistory, Geofence, dan
     * Notification ikut terhapus otomatis lewat cascade delete di database.
     * --------------------------------------------------------------------------
     */
    public function destroy(Device $device): void
    {
        $device->delete();
    }

    /**
     * --------------------------------------------------------------------------
     * Update Vehicle Information
     * --------------------------------------------------------------------------
     */
    public function updateInformation(
        Device $device,
        array $data
    ): array {

        /*
    |--------------------------------------------------------------------------
    | Load Vehicle
    |--------------------------------------------------------------------------
    */

        $device->load('vehicle');

        /*
    |--------------------------------------------------------------------------
    | Update Vehicle
    |--------------------------------------------------------------------------
    */

        $device->vehicle->update([

            'vehicle_name' => trim($data['vehicle_name']),

            'plate_number' => strtoupper(
                trim($data['plate_number'])
            ),

            'vehicle_type' => strtolower(
                trim($data['vehicle_type'])
            ),

        ]);

        /*
    |--------------------------------------------------------------------------
    | Refresh Model
    |--------------------------------------------------------------------------
    */

        $device->vehicle->refresh();

        /*
    |--------------------------------------------------------------------------
    | Return
    |--------------------------------------------------------------------------
    */

        return [

            'vehicle_name' => $device->vehicle->vehicle_name,

            'plate_number' => $device->vehicle->plate_number,

            'vehicle_type' => $device->vehicle->vehicle_type,

        ];
    }
    /**
     * --------------------------------------------------------------------------
     * Latest Location
     * --------------------------------------------------------------------------
     */
    public function latest(
        Device $device
    ): array {

        $latestLog = $device->deviceLogs()

            ->select(['payload', 'received_at'])

            ->latest('received_at')

            ->first();

        if (! $latestLog) {

            return [];
        }

        return $this->transformLatestLocation(

            $latestLog->payload,

            $latestLog->received_at,

            null

        );
    }

    /**
     * --------------------------------------------------------------------------
     * Travel History
     * --------------------------------------------------------------------------
     */
    public function history(
        Device $device,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {

        $query = $device->travelHistories()

            ->select(self::TRAVEL_HISTORY_COLUMNS)

            ->orderBy('received_at');

        if ($startDate) {

            $query->whereDate(
                'received_at',
                '>=',
                $startDate
            );
        }

        if ($endDate) {

            $query->whereDate(
                'received_at',
                '<=',
                $endDate
            );
        }

        $histories = $query

            ->limit(self::MAX_HISTORY_ROWS)

            ->get();

        return $this->transformTravelCollection(

            $histories

        );
    }

    /**
     * --------------------------------------------------------------------------
     * Playback
     * --------------------------------------------------------------------------
     */
    public function playback(
        Device $device,
        ?string $date = null
    ): array {

        $query = $device->travelHistories()

            ->select(self::TRAVEL_HISTORY_COLUMNS)

            ->orderBy('received_at');

        if ($date) {

            $query->whereDate(

                'received_at',

                $date

            );
        }

        return $this->transformTravelCollection(

            $query

                ->limit(self::MAX_HISTORY_ROWS)

                ->get()

        );
    }

    /**
     * --------------------------------------------------------------------------
     * Summary
     * --------------------------------------------------------------------------
     */
    public function summary(
        Device $device
    ): array {

        $today = $device->travelHistories()

            ->select(['location', 'received_at'])

            ->whereDate(
                'received_at',
                today()
            )

            ->orderBy('received_at')

            ->get();

        if ($today->isEmpty()) {

            return [

                'total_distance' => 0,

                'max_speed' => 0,

                'average_speed' => 0,

                'moving_time' => 0,

                'stop_time' => 0,

                'total_points' => 0,

            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Speed
        |--------------------------------------------------------------------------
        */

        $maxSpeed = round(

            (float) $today->max(

                fn(TravelHistory $history) =>

                $history->location['speed'] ?? 0

            ),

            2

        );

        $averageSpeed = round(

            (float) $today->avg(

                fn(TravelHistory $history) =>

                $history->location['speed'] ?? 0

            ),

            2

        );

        /*
        |--------------------------------------------------------------------------
        | Moving / Stop Time
        |--------------------------------------------------------------------------
        */

        $movingTime = 0;

        $stopTime = 0;

        for (

            $i = 1;

            $i < $today->count();

            $i++

        ) {

            /** @var TravelHistory $previous */
            $previous = $today[$i - 1];

            /** @var TravelHistory $current */
            $current = $today[$i];

            $seconds = $previous->received_at
                ->diffInSeconds(
                    $current->received_at
                );

            $speed = (float) (

                $current->location['speed']

                ??

                0

            );

            if ($speed > 0) {

                $movingTime += $seconds;
            } else {

                $stopTime += $seconds;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Distance
        |--------------------------------------------------------------------------
        */

        $totalDistance = $this->calculateTotalDistance(

            $today

        );

        /*
        |--------------------------------------------------------------------------
        | Return
        |--------------------------------------------------------------------------
        */

        return [

            'total_distance' => $totalDistance,

            'max_speed' => $maxSpeed,

            'average_speed' => $averageSpeed,

            'moving_time' => $movingTime,

            'stop_time' => $stopTime,

            'total_points' => $today->count(),

        ];
    }

    /**
     * --------------------------------------------------------------------------
     * Activity Timeline
     * --------------------------------------------------------------------------
     */
    public function activity(
        Device $device
    ): array {

        $activities = $device->travelHistories()

            ->select(self::TRAVEL_HISTORY_COLUMNS)

            ->orderByDesc('received_at')

            ->limit(100)

            ->get()

            ->map(function (TravelHistory $history) {

                $data = $this->transformTravelHistory(
                    $history
                );

                $speed = $data['speed'];

                return [

                    'id' => $data['id'],

                    'type' => $speed > 0
                        ? 'moving'
                        : 'stop',

                    'title' => $speed > 0
                        ? 'Kendaraan Bergerak'
                        : 'Kendaraan Berhenti',

                    'lat' => $data['lat'],

                    'lng' => $data['lng'],

                    'speed' => $data['speed'],

                    'heading' => $data['heading'],

                    'battery' => $data['battery'],

                    'satellite' => $data['satellite'],

                    'address' => $data['address'],

                    'received_at' => $data['received_at'],

                ];
            })

            ->values()

            ->toArray();

        return $activities;
    }

    /**
     * --------------------------------------------------------------------------
     * Stop History
     * --------------------------------------------------------------------------
     */
    public function stop(
        Device $device
    ): array {

        return $device->stopHistories()

            ->select(self::STOP_HISTORY_COLUMNS)

            ->orderByDesc('start_time')

            ->limit(self::MAX_HISTORY_ROWS)

            ->get()

            ->map(function (StopHistory $stop) {

                $location = $stop->location ?? [];

                return [

                    'id' => $stop->id,

                    'lat' => isset($location['lat'])
                        ? (float) $location['lat']
                        : null,

                    'lng' => isset($location['lng'])
                        ? (float) $location['lng']
                        : null,

                    'address' => $stop->search_address,

                    'started_at' => optional(
                        $stop->start_time
                    )?->toDateTimeString(),

                    'ended_at' => optional(
                        $stop->end_time
                    )?->toDateTimeString(),

                    'duration_seconds' => $stop->duration_seconds,

                ];
            })

            ->values()

            ->toArray();
    }
}
