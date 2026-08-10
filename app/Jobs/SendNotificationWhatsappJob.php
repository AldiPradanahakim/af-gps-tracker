<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Services\Notification\WhatsappService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SendNotificationWhatsappJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [10, 30, 60];

    public function __construct(
        public string $notificationId
    ) {}

    /**
     * Kegagalan WhatsApp tidak boleh menghentikan monitoring kendaraan (BR-007).
     * Job berjalan async di queue, terpisah dari proses ingest GPS.
     */
    public function handle(WhatsappService $whatsappService): void
    {
        $notification = Notification::with(['device.user', 'device.vehicle'])
            ->find($this->notificationId);

        if (! $notification) {
            return;
        }

        $whatsappService->send($notification);
    }

    /**
     * Job gagal setelah seluruh percobaan habis: catat error, jangan rethrow.
     */
    public function failed(Throwable $exception): void
    {
        report($exception);
    }
}
