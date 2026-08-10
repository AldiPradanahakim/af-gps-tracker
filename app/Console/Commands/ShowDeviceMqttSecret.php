<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Services\MQTT\MQTTSignatureService;
use Illuminate\Console\Command;

class ShowDeviceMqttSecret extends Command
{
    /**
     * Artisan Command.
     */
    protected $signature = 'device:mqtt-secret {device_id} {--rotate : Buat mqtt_secret baru, mencabut akses secret lama}';

    /**
     * Command Description.
     */
    protected $description = 'Tampilkan mqtt_secret sebuah device untuk dipasang di firmware, plus contoh payload MQTT yang sudah ditandatangani.';

    public function handle(MQTTSignatureService $signatureService): int
    {
        $device = Device::query()
            ->where('device_id', $this->argument('device_id'))
            ->first();

        if (! $device) {

            $this->error('Device tidak ditemukan.');

            return self::FAILURE;
        }

        if ($this->option('rotate')) {

            if (! $this->confirm(
                "Secret lama untuk device [{$device->device_id}] akan langsung tidak berlaku - firmware yang belum diupdate akan berhenti terverifikasi. Lanjutkan?"
            )) {

                $this->info('Dibatalkan.');

                return self::SUCCESS;
            }

            $device->update([
                'mqtt_secret' => bin2hex(random_bytes(32)),
            ]);

            $this->warn('mqtt_secret baru dibuat. Secret lama sudah tidak berlaku - update firmware device ini secepatnya.');

            $this->newLine();
        }

        $this->info('Device    : ' . $device->device_id);
        $this->info('Secret    : ' . $device->mqtt_secret);

        $this->newLine();
        $this->line('Cara pakai: firmware menghitung HMAC-SHA256 dari string');
        $this->line('"message_id=..&lat=..&lng=..&speed=..&heading=..&battery=..&satellite=..&received_at=.."');
        $this->line('(field dalam urutan itu persis, dipisah "&") memakai secret di atas sebagai key,');
        $this->line('lalu kirim hasilnya (hex) sebagai field "signature" di payload JSON.');

        $samplePayload = [
            'device_id' => $device->device_id,
            'message_id' => (string) now()->timestamp,
            'lat' => -6.19524,
            'lng' => 106.82301,
            'speed' => 0,
            'heading' => 0,
            'battery' => 90,
            'satellite' => 10,
            'received_at' => now()->toIso8601String(),
        ];

        $samplePayload['signature'] = $signatureService->sign($device, $samplePayload);

        $this->newLine();
        $this->line('Contoh payload valid untuk testing (mis. via mosquitto_pub):');
        $this->line(json_encode($samplePayload, JSON_PRETTY_PRINT));

        return self::SUCCESS;
    }
}
