<?php

namespace App\Services\Notification;

use App\Models\Device;
use App\Models\Geofence;
use App\Models\Notification;
use App\Models\StopHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class NotificationService
{
    /**
     * Create stop notification.
     */
    public function createStopNotification(
        Device $device,
        StopHistory $stopHistory
    ): Notification {

        return DB::transaction(function () use (
            $device,
            $stopHistory
        ) {

            $notification = Notification::create([

                'device_id' => $device->id,

                'stop_history_id' => $stopHistory->id,

                'type' => 'stop',

                'data' => [

                    'title' => 'Kendaraan Berhenti',

                    'message' => sprintf(
                        'Kendaraan berhenti selama %d menit.',
                        (int) floor(
                            $stopHistory->duration_seconds / 60
                        )
                    ),

                    'location' => $stopHistory->location,

                    'search_address' => $stopHistory->search_address,

                    'start_time' => $stopHistory->start_time,

                    'end_time' => $stopHistory->end_time,

                ],

                'status' => 'pending',

            ]);

            $this->dispatch(
                $notification
            );

            return $notification;
        });
    }

    /**
     * Create geofence notification.
     */
    public function createGeofenceExitNotification(
        Device $device,
        Geofence $geofence,
        array $payload
    ): Notification {

        return DB::transaction(function () use (
            $device,
            $geofence,
            $payload
        ) {

            $notification = Notification::create([

                'device_id' => $device->id,

                'geofence_id' => $geofence->id,

                'type' => 'geofence_exit',

                'data' => [

                    'title' => 'Keluar Geofence',

                    'message' => sprintf(
                        'Kendaraan keluar dari area "%s".',
                        $geofence->name
                    ),

                    'location' => [

                        'lat' => $payload['lat'],

                        'lng' => $payload['lng'],

                    ],

                    'search_address' => $payload['search_address'] ?? null,

                ],

                'status' => 'pending',

            ]);

            $this->dispatch(
                $notification
            );

            return $notification;
        });
    }

    /**
     * Dispatch notification.
     */
    protected function dispatch(
        Notification $notification
    ): void {

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Web Notification
            |--------------------------------------------------------------------------
            |
            | Notification otomatis tersedia
            | di halaman web.
            |
            */

            /*
            |--------------------------------------------------------------------------
            | Realtime Broadcast
            |--------------------------------------------------------------------------
            |
            | Akan dipindahkan ke
            | RealtimeService.
            |
            */

            /*
            |--------------------------------------------------------------------------
            | Email
            |--------------------------------------------------------------------------
            | Notifikasi tipe "stop" memakai pengaturan Stop Detection
            | (device.stop_setting), bukan pengaturan notifikasi umum.
            |--------------------------------------------------------------------------
            */

            if ($this->isChannelEnabled($notification, 'email')) {

                $this->sendEmail(
                    $notification
                );
            }

            /*
            |--------------------------------------------------------------------------
            | WhatsApp
            |--------------------------------------------------------------------------
            */

            if ($this->isChannelEnabled($notification, 'whatsapp')) {

                $this->sendWhatsapp(
                    $notification
                );
            }

            $notification->update([

                'status' => 'sent',

                'sent_at' => Carbon::now(),

            ]);

            DB::commit();
        } catch (Throwable $exception) {

            DB::rollBack();

            report($exception);

            $notification->update([

                'status' => 'failed',

            ]);
        }
    }

    /**
     * Cek apakah channel notifikasi (email/whatsapp) aktif untuk
     * notifikasi ini. Notifikasi tipe "stop" mengikuti pengaturan
     * Stop Detection (device.stop_setting), tipe lain mengikuti
     * pengaturan notifikasi umum (device.notification_setting).
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

        return (bool) data_get(
            $notification->device,
            "notification_setting.{$channel}",
            false
        );
    }

    /**
     * Send Email Notification.
     */
    protected function sendEmail(
        Notification $notification
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Akan dibuat pada EmailService
        |--------------------------------------------------------------------------
        */
    }

    /**
     * Send WhatsApp Notification.
     */
    protected function sendWhatsapp(
        Notification $notification
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Akan dibuat pada WhatsappService
        |--------------------------------------------------------------------------
        */
    }
}
