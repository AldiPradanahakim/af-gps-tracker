<?php

namespace App\Services\Notification;

use App\Models\Notification;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WhatsappService
{
    /**
     * Kirim notification lewat WhatsApp (Fonnte).
     *
     * @throws RuntimeException
     */
    public function send(Notification $notification): void
    {
        $token = config('services.fonnte.token');

        if (! $token) {

            throw new RuntimeException(
                'FONNTE_TOKEN belum dikonfigurasi.'
            );
        }

        $phone = $this->normalizePhone(
            $notification->device?->user?->phone
        );

        if (! $phone) {

            throw new RuntimeException(
                'Nomor WhatsApp tujuan tidak ditemukan untuk notification ini.'
            );
        }

        $response = Http::asForm()

            ->withHeaders([
                'Authorization' => $token,
            ])

            ->timeout(15)

            ->post(config('services.fonnte.url'), [

                'target' => $phone,

                'message' => $this->buildMessage($notification),

            ]);

        if (! $response->successful()) {

            throw new RuntimeException(
                "Fonnte API gagal dengan status {$response->status()}: {$response->body()}"
            );
        }

        $body = $response->json();

        if (is_array($body) && array_key_exists('status', $body) && $body['status'] === false) {

            throw new RuntimeException(
                'Fonnte API menolak pesan: ' . ($body['reason'] ?? 'unknown error')
            );
        }
    }

    /**
     * Susun teks pesan WhatsApp dari data notification.
     *
     * Format meniru template notifikasi standar (header ber-emoji,
     * detail kendaraan/geofence, waktu WIB, koordinat + link peta,
     * pesan status) supaya jelas dan lengkap dibaca lewat WhatsApp.
     */
    protected function buildMessage(Notification $notification): string
    {
        $appName = config('app.name');

        $vehicleName = data_get($notification->data, 'vehicle_name')
            ?? $notification->device?->vehicle?->vehicle_name
            ?? '-';

        $plateNumber = data_get($notification->data, 'plate_number')
            ?? $notification->device?->vehicle?->plate_number
            ?? '-';

        $statusLabel = data_get($notification->data, 'title', 'Notifikasi Kendaraan');

        $headerEmoji = match ($notification->type) {
            'geofence_exit' => '🚨',
            'geofence_enter' => '🟢',
            'stop' => '🛑',
            default => '🔔',
        };

        $latitude = data_get($notification->data, 'location.lat');

        $longitude = data_get($notification->data, 'location.lng');

        $lines = [

            "{$headerEmoji} *NOTIFIKASI " . mb_strtoupper($appName) . '*',

            '',

            "🚗 Kendaraan: {$vehicleName}",

            "🔖 Plat Nomor: {$plateNumber}",

            "📍 Status: {$statusLabel}",

        ];

        $geofenceName = data_get($notification->data, 'geofence_name');

        if ($geofenceName) {

            $lines[] = "🛡️ Geofence: {$geofenceName}";
        }

        $durationMinutes = data_get($notification->data, 'duration_minutes');

        if ($durationMinutes !== null) {

            $lines[] = "⏱️ Durasi Berhenti: {$durationMinutes} menit";
        }

        $lines[] = '';

        $lines[] = '🕐 Waktu: ' . NotificationPresenter::formatDateTime($notification->created_at);

        $address = data_get($notification->data, 'search_address');

        if ($address) {

            $lines[] = "📌 Lokasi: {$address}";
        }

        if ($latitude !== null && $longitude !== null) {

            $lines[] = "🌐 Koordinat: {$latitude}, {$longitude}";
        }

        $lines[] = '🔗 Lihat Lokasi: ' . NotificationPresenter::publicTrackingLink($notification);

        $lines[] = '';

        $lines[] = data_get($notification->data, 'message', '-');

        $lines[] = '';

        $lines[] = $appName;

        return implode("\n", $lines);
    }

    /**
     * Normalisasi nomor telepon ke format yang diterima Fonnte (62xxxxxxxxxx).
     */
    protected function normalizePhone(?string $phone): ?string
    {
        if (! $phone) {

            return null;
        }

        $digits = preg_replace('/\D/', '', $phone);

        if (! $digits) {

            return null;
        }

        if (str_starts_with($digits, '0')) {

            $digits = '62' . substr($digits, 1);
        }

        if (! str_starts_with($digits, '62')) {

            $digits = '62' . $digits;
        }

        return $digits;
    }
}
