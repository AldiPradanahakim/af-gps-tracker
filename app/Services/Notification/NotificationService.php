<?php

namespace App\Services\Notification;

use App\Events\NotificationCreated;
use App\Jobs\SendNotificationEmailJob;
use App\Jobs\SendNotificationWhatsappJob;
use App\Models\Device;
use App\Models\Geofence;
use App\Models\Notification;
use App\Models\StopHistory;
use App\Models\User;
use App\Repositories\NotificationRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Throwable;

class NotificationService
{
    public function __construct(
        protected NotificationRepository $notificationRepository
    ) {}

    /**
     * Create stop notification.
     */
    public function createStopNotification(
        Device $device,
        StopHistory $stopHistory
    ): Notification {

        $durationMinutes = (int) floor(
            $stopHistory->duration_seconds / 60
        );

        $notification = $this->notificationRepository->create([

            'device_id' => $device->id,

            'stop_history_id' => $stopHistory->id,

            'type' => 'stop',

            'data' => [

                'title' => 'Kendaraan Berhenti',

                'message' => sprintf(
                    'Kendaraan berhenti selama %d menit.',
                    $durationMinutes
                ),

                'duration_minutes' => $durationMinutes,

                'location' => $stopHistory->location,

                'search_address' => $stopHistory->search_address,

                'start_time' => $stopHistory->start_time,

                'end_time' => $stopHistory->end_time,

                ...$this->vehicleData($device),

            ],

            'status' => 'pending',

        ]);

        $this->dispatch(
            $notification
        );

        return $notification;
    }

    /**
     * Create geofence exit notification.
     */
    public function createGeofenceExitNotification(
        Device $device,
        Geofence $geofence,
        array $payload
    ): Notification {

        return $this->createGeofenceNotification(
            $device,
            $geofence,
            $payload,
            exited: true
        );
    }

    /**
     * Create geofence enter notification.
     */
    public function createGeofenceEnterNotification(
        Device $device,
        Geofence $geofence,
        array $payload
    ): Notification {

        return $this->createGeofenceNotification(
            $device,
            $geofence,
            $payload,
            exited: false
        );
    }

    /**
     * Create geofence "masih di luar area" reminder.
     *
     * Memakai tipe geofence_exit yang sama supaya pengingat ikut
     * tersaring bersama notifikasi keluar area (dan mengikuti toggle
     * Email/WhatsApp yang sama), tetapi ditandai data.repeat = true
     * supaya bisa dibedakan di tampilan.
     */
    public function createGeofenceStillOutsideNotification(
        Device $device,
        Geofence $geofence,
        array $payload,
        int $minutesOutside
    ): Notification {

        return $this->createGeofenceNotification(
            $device,
            $geofence,
            $payload,
            exited: true,
            repeat: true,
            minutesOutside: $minutesOutside
        );
    }

    /**
     * Create geofence notification (enter/exit).
     */
    protected function createGeofenceNotification(
        Device $device,
        Geofence $geofence,
        array $payload,
        bool $exited,
        bool $repeat = false,
        int $minutesOutside = 0
    ): Notification {

        /*
        |--------------------------------------------------------------------------
        | Tipe area ikut disebut supaya pengguna langsung tahu geofence
        | MANA yang dilewati - satu kendaraan bisa punya Radius,
        | Administratif, dan Polygon sekaligus, masing-masing dengan
        | notifikasinya sendiri.
        |--------------------------------------------------------------------------
        */

        $area = sprintf(
            '"%s" (%s)',
            $geofence->name,
            $geofence->type_label
        );

        if ($repeat) {

            $title = 'Masih di Luar Geofence';

            $message = $minutesOutside > 0
                ? sprintf(
                    'Kendaraan masih berada di luar area %s sejak %s lalu.',
                    $area,
                    $this->humanMinutes($minutesOutside)
                )
                : sprintf(
                    'Kendaraan masih berada di luar area %s.',
                    $area
                );

        } else {

            $title = $exited ? 'Keluar Geofence' : 'Masuk Geofence';

            $message = sprintf(

                $exited
                    ? 'Kendaraan keluar dari area %s.'
                    : 'Kendaraan masuk ke area %s.',

                $area

            );
        }

        $notification = $this->notificationRepository->create([

            'device_id' => $device->id,

            'geofence_id' => $geofence->id,

            'type' => $exited ? 'geofence_exit' : 'geofence_enter',

            'data' => [

                'title' => $title,

                'message' => $message,

                'geofence_name' => $geofence->name,

                'geofence_type' => $geofence->type,

                'geofence_type_label' => $geofence->type_label,

                'repeat' => $repeat,

                'minutes_outside' => $repeat ? $minutesOutside : null,

                'location' => [

                    'lat' => $payload['lat'],

                    'lng' => $payload['lng'],

                ],

                'search_address' => $payload['search_address'] ?? null,

                ...$this->vehicleData($device),

            ],

            'status' => 'pending',

        ]);

        $this->dispatch(
            $notification
        );

        return $notification;
    }

    /**
     * "95" -> "1 jam 35 menit", "40" -> "40 menit".
     */
    protected function humanMinutes(int $minutes): string
    {
        if ($minutes < 60) {

            return $minutes . ' menit';
        }

        $hours = intdiv($minutes, 60);

        $rest = $minutes % 60;

        return $rest > 0
            ? sprintf('%d jam %d menit', $hours, $rest)
            : sprintf('%d jam', $hours);
    }

    /**
     * Create overspeed notification.
     */
    public function createOverspeedNotification(
        Device $device,
        array $payload
    ): Notification {

        $limit = (int) data_get(
            $device,
            'speed_setting.limit_kmh',
            80
        );

        $notification = $this->notificationRepository->create([

            'device_id' => $device->id,

            'type' => 'overspeed',

            'data' => [

                'title' => 'Kecepatan Berlebih',

                'message' => sprintf(
                    'Kendaraan melaju %s km/j, melebihi batas %d km/j.',
                    number_format((float) ($payload['speed'] ?? 0), 0),
                    $limit
                ),

                'speed' => (float) ($payload['speed'] ?? 0),

                'limit_kmh' => $limit,

                'location' => [

                    'lat' => $payload['lat'],

                    'lng' => $payload['lng'],

                ],

                'search_address' => $payload['search_address'] ?? null,

                ...$this->vehicleData($device),

            ],

            'status' => 'pending',

        ]);

        $this->dispatch(
            $notification
        );

        return $notification;
    }

    /**
     * Create low battery notification.
     */
    public function createLowBatteryNotification(
        Device $device,
        int $battery
    ): Notification {

        $notification = $this->notificationRepository->create([

            'device_id' => $device->id,

            'type' => 'low_battery',

            'data' => [

                'title' => 'Baterai Perangkat Lemah',

                'message' => sprintf(
                    'Baterai perangkat GPS tersisa %d%%. Segera isi ulang.',
                    $battery
                ),

                'battery' => $battery,

                ...$this->vehicleData($device),

            ],

            'status' => 'pending',

        ]);

        $this->dispatch(
            $notification
        );

        return $notification;
    }

    /**
     * Create device offline notification.
     */
    public function createDeviceOfflineNotification(
        Device $device
    ): Notification {

        $notification = $this->notificationRepository->create([

            'device_id' => $device->id,

            'type' => 'device_offline',

            'data' => [

                'title' => 'Perangkat Terputus',

                'message' => sprintf(
                    'Perangkat GPS %s berhenti mengirim data. Terakhir terlihat %s.',
                    $device->device_id,
                    optional($device->last_heartbeat)
                        ?->locale('id')
                        ?->diffForHumans() ?? 'tidak diketahui',
                ),

                'last_heartbeat' => optional(
                    $device->last_heartbeat
                )?->toISOString(),

                ...$this->vehicleData($device),

            ],

            'status' => 'pending',

        ]);

        $this->dispatch(
            $notification
        );

        return $notification;
    }

    /**
     * Create device online (kembali online) notification.
     */
    public function createDeviceOnlineNotification(
        Device $device
    ): Notification {

        $notification = $this->notificationRepository->create([

            'device_id' => $device->id,

            'type' => 'device_online',

            'data' => [

                'title' => 'Perangkat Terhubung Kembali',

                'message' => sprintf(
                    'Perangkat GPS %s kembali mengirim data.',
                    $device->device_id,
                ),

                ...$this->vehicleData($device),

            ],

            'status' => 'pending',

        ]);

        $this->dispatch(
            $notification
        );

        return $notification;
    }

    /**
     * Data kendaraan yang disisipkan ke dalam payload notification.
     */
    protected function vehicleData(Device $device): array
    {
        $vehicle = $device->relationLoaded('vehicle')
            ? $device->vehicle
            : $device->vehicle()->first();

        return [

            'device_id' => $device->id,

            'vehicle_name' => $vehicle?->vehicle_name,

            'plate_number' => $vehicle?->plate_number,

        ];
    }

    /**
     * Dispatch notification: System (instan), Realtime Broadcast,
     * Email, dan WhatsApp berdasarkan Notification Setting.
     *
     * Kegagalan Email/WhatsApp tidak boleh menghentikan monitoring
     * (BR-006, BR-007) - keduanya dikirim lewat queued job terpisah.
     */
    protected function dispatch(
        Notification $notification
    ): void {

        try {

            $notification->load([
                'device.user',
                'device.vehicle',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Web Notification
            |--------------------------------------------------------------------------
            |
            | Notification otomatis tersedia di halaman web
            | begitu record tersimpan.
            |
            */

            $notification->update([

                'status' => 'sent',

                'sent_at' => Carbon::now(),

            ]);

            /*
            |--------------------------------------------------------------------------
            | Realtime Broadcast
            |--------------------------------------------------------------------------
            */

            $userId = $notification->device?->user_id;

            if ($userId) {

                event(

                    new NotificationCreated(

                        userId: (string) $userId,

                        payload: $notification->toArray(),

                    )

                );
            }

            /*
            |--------------------------------------------------------------------------
            | Email
            |--------------------------------------------------------------------------
            */

            if ($this->isChannelEnabled($notification, 'email')) {

                SendNotificationEmailJob::dispatch(
                    $notification->id
                );
            }

            /*
            |--------------------------------------------------------------------------
            | WhatsApp
            |--------------------------------------------------------------------------
            */

            if ($this->isChannelEnabled($notification, 'whatsapp')) {

                SendNotificationWhatsappJob::dispatch(
                    $notification->id
                );
            }
        } catch (Throwable $exception) {

            report($exception);

            $notification->update([

                'status' => 'failed',

            ]);
        }
    }

    /**
     * Cek apakah channel notifikasi (email/whatsapp) aktif untuk
     * notifikasi ini. Notifikasi tipe "stop" mengikuti pengaturan
     * Stop Detection (device.stop_setting), tipe "overspeed" mengikuti
     * pengaturan Batas Kecepatan (device.speed_setting) - masing-masing
     * tab punya toggle Email/WhatsApp sendiri. Tipe lain (geofence,
     * device online/offline, dst) mengikuti pengaturan notifikasi umum
     * (device.notification_setting, tab Geofence).
     */
    protected function isChannelEnabled(
        Notification $notification,
        string $channel
    ): bool {

        if ($notification->type === 'stop') {

            return (bool) data_get(
                $notification->device,
                "stop_setting.{$channel}_notification",
                false
            );
        }

        if ($notification->type === 'overspeed') {

            return (bool) data_get(
                $notification->device,
                "speed_setting.{$channel}_notification",
                false
            );
        }

        return (bool) data_get(
            $notification->device,
            "notification_setting.{$channel}",
            false
        );
    }

    /**
     * Notification milik user yang belum dibaca (untuk dropdown).
     *
     * Sumber tampilan awal (refresh/login/masuk halaman) - notification
     * yang sudah dibaca tidak ditampilkan lagi.
     */
    public function unreadForUser(
        User $user,
        int $limit = 50
    ): Collection {

        return $this->notificationRepository->unreadByUser(
            (string) $user->id,
            $limit
        );
    }

    /**
     * Daftar notification milik user (untuk endpoint listing).
     */
    public function listForUser(
        User $user,
        int $perPage = 20
    ): LengthAwarePaginator {

        return $this->notificationRepository->listByUser(
            (string) $user->id,
            $perPage
        );
    }

    /**
     * Cari notification milik device tertentu.
     *
     * Dipakai untuk fokus peta di halaman Detail Kendaraan (query
     * string ?event=) - lihat VehicleService::show().
     */
    public function findForDevice(
        string $notificationId,
        string $deviceId
    ): ?Notification {

        return $this->notificationRepository->findForDevice(
            $notificationId,
            $deviceId
        );
    }

    /**
     * Tandai satu notification sebagai telah dibaca.
     */
    public function markAsRead(
        User $user,
        string $notificationId
    ): Notification {

        $notification = $this->notificationRepository->findOwnedByUser(
            $notificationId,
            (string) $user->id
        );

        abort_if(
            ! $notification,
            404,
            'Notifikasi tidak ditemukan.'
        );

        return $this->notificationRepository->markAsRead(
            $notification
        );
    }

    /**
     * Tandai seluruh notification milik user sebagai telah dibaca.
     */
    public function markAllAsRead(
        User $user
    ): int {

        return $this->notificationRepository->markAllAsReadByUser(
            (string) $user->id
        );
    }
}
