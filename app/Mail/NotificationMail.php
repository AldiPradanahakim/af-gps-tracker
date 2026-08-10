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
        $latitude = data_get($this->notification->data, 'location.lat');

        $longitude = data_get($this->notification->data, 'location.lng');

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
                'searchAddress' => data_get($this->notification->data, 'search_address'),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'trackingLink' => NotificationPresenter::publicTrackingLink($this->notification),
                'formattedTime' => NotificationPresenter::formatDateTime($this->notification->created_at),
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
