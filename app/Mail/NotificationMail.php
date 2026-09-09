<?php

namespace App\Mail;

use App\Models\Notification;
use App\Services\Notification\NotificationPresenter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Notification $notification
    ) {}

    public function envelope(): Envelope
    {
        $title = (string) data_get(
            $this->notification->data,
            'title',
            'Notifikasi Kendaraan'
        );

        $vehicleName = data_get($this->notification->data, 'vehicle_name')
            ?? $this->notification->device?->vehicle?->vehicle_name;

        return new Envelope(
            subject: $this->headerEmoji() . ' ' . $title . ($vehicleName ? " - {$vehicleName}" : ''),
        );
    }

    public function content(): Content
    {
        /*
        |--------------------------------------------------------------------------
        | Alamat & Koordinat
        |--------------------------------------------------------------------------
        |
        | Di-resolve di sini (queued job, boleh menunggu) supaya email
        | tidak pernah lagi memuat placeholder "Memuat alamat..." atau
        | menautkan peta kosong untuk notifikasi yang payload-nya tidak
        | membawa koordinat (baterai lemah, perangkat offline/online).
        |
        */

        $location = NotificationPresenter::resolveLocation($this->notification);

        $searchAddress = NotificationPresenter::resolveAddress($this->notification);

        $latitude = $location['lat'];

        $longitude = $location['lng'];

        return new Content(
            markdown: 'emails.notification',
            with: [
                'headerEmoji' => $this->headerEmoji(),
                'title' => data_get($this->notification->data, 'title', 'Notifikasi Kendaraan'),
                'message' => data_get($this->notification->data, 'message', '-'),
                'vehicleName' => data_get($this->notification->data, 'vehicle_name')
                    ?? $this->notification->device?->vehicle?->vehicle_name,
                'plateNumber' => data_get($this->notification->data, 'plate_number')
                    ?? $this->notification->device?->vehicle?->plate_number,
                'geofenceName' => data_get($this->notification->data, 'geofence_name'),
                'durationMinutes' => data_get($this->notification->data, 'duration_minutes'),
                'searchAddress' => $searchAddress,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'mapsLink' => NotificationPresenter::mapsLink($latitude, $longitude),

                'trackingLink' => ($latitude !== null && $longitude !== null)
                    ? NotificationPresenter::publicTrackingLink($this->notification)
                    : null,
                /*
                |----------------------------------------------------------
                | Jam di email mengikuti zona waktu PENERIMA (WIB/WITA/
                | WIT sesuai pilihannya), bukan zona waktu server.
                |----------------------------------------------------------
                */

                'formattedTime' => NotificationPresenter::formatDateTime(
                    $this->notification->created_at,
                    NotificationPresenter::recipient($this->notification)
                ),
            ],
        );
    }

    /**
     * Emoji header berdasarkan tipe notification.
     */
    protected function headerEmoji(): string
    {
        return match ($this->notification->type) {
            'geofence_exit' => '🚨',
            'geofence_enter' => '🟢',
            'stop' => '🛑',
            default => '🔔',
        };
    }
}
