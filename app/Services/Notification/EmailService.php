<?php

namespace App\Services\Notification;

use App\Mail\NotificationMail;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class EmailService
{
    /**
     * Kirim notification lewat Email (SMTP Gmail).
     *
     * @throws RuntimeException
     */
    public function send(Notification $notification): void
    {
        $email = $notification->device?->user?->email;

        if (! $email) {

            throw new RuntimeException(
                'Email tujuan tidak ditemukan untuk notification ini.'
            );
        }

        Mail::to($email)->send(
            new NotificationMail($notification)
        );
    }
}
