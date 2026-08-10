<?php

use App\Models\Device;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * mqtt_secret: kunci HMAC per-device supaya payload GPS via MQTT bisa
     * diverifikasi benar-benar berasal dari device tersebut (bukan hanya
     * device_id yang dicantumkan di JSON, yang mana pun bisa memalsukan).
     * Disimpan encrypted (bukan hashed) karena servernya butuh nilai asli
     * untuk menghitung ulang HMAC saat verifikasi.
     */
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->text('mqtt_secret')->nullable()->after('device_password');
        });

        // Backfill device yang sudah ada (mis. dari DemoAccountSeeder)
        // supaya tidak mendadak kehilangan akses MQTT setelah migration ini.
        Device::whereNull('mqtt_secret')->each(function (Device $device) {
            $device->update([
                'mqtt_secret' => bin2hex(random_bytes(32)),
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('mqtt_secret');
        });
    }
};
