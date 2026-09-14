<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Pengaturan Geofence per Kendaraan
            |--------------------------------------------------------------------------
            |
            | Bentuknya mengikuti stop_setting / speed_setting supaya
            | konsisten:
            |
            |   {
            |     "repeat_enabled": bool,   pengingat "masih di luar area"
            |     "repeat_minutes": int     jeda antar pengingat (menit)
            |   }
            |
            | Kanal email/WhatsApp TIDAK diduplikasi di sini - pengingat
            | tetap mengikuti devices.notification_setting yang sudah
            | dipakai notifikasi geofence biasa.
            |--------------------------------------------------------------------------
            */

            $table->json('geofence_setting')
                ->nullable()
                ->after('speed_setting');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {

            $table->dropColumn('geofence_setting');
        });
    }
};
