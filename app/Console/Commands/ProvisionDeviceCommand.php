<?php

namespace App\Console\Commands;

use App\Models\Device;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ProvisionDeviceCommand extends Command
{
    /**
     * Artisan Command.
     */
    protected $signature = 'device:provision
        {device_id : Kode unik perangkat, mis. GPS-AF-0002}
        {--password= : Password aktivasi (kosongkan untuk digenerate acak)}';

    /**
     * Command Description.
     */
    protected $description = 'Provisioning device GPS fisik baru (belum diaktivasi user) ke database, sebelum unit dikirim/dipasang.';

    public function handle(): int
    {
        $deviceId = trim(
            (string) $this->argument('device_id')
        );

        if ($deviceId === '') {

            $this->error('device_id wajib diisi.');

            return self::FAILURE;
        }

        if (Device::query()->where('device_id', $deviceId)->exists()) {

            $this->error("Device [{$deviceId}] sudah terdaftar.");

            return self::FAILURE;
        }

        $password = $this->option('password')
            ?: Str::password(12, symbols: false);

        $device = Device::create([

            'device_id' => $deviceId,

            'device_password' => $password,

            'user_id' => null,

            'is_active' => false,

            'is_inside_geofence' => false,

        ]);

        $this->info('Device berhasil di-provisioning.');

        $this->newLine();

        $this->table(
            ['Field', 'Nilai'],
            [
                ['device_id', $device->device_id],
                ['device_password', $password],
                ['mqtt_secret', $device->mqtt_secret],
            ]
        );

        $this->newLine();

        $this->warn('Catat device_password sekarang - nilainya disimpan ter-hash dan tidak bisa ditampilkan ulang.');

        $this->line('device_password: dipakai user untuk klaim/aktivasi device di halaman /devices/activate.');

        $this->line('mqtt_secret: dipasang ke firmware untuk menandatangani payload MQTT (lihat: php artisan device:mqtt-secret ' . $deviceId . ').');

        return self::SUCCESS;
    }
}
