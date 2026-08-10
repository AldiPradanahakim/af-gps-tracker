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
            | Battery Alert
            |--------------------------------------------------------------------------
            |
            | last_battery didenormalisasi dari payload GPS terbaru supaya
            | job berkala pengecek baterai rendah tidak perlu query
            | device_logs per device (unbounded json scan).
            |--------------------------------------------------------------------------
            */

            $table->unsignedTinyInteger('last_battery')->nullable()->after('last_heartbeat');

            /*
            |--------------------------------------------------------------------------
            | Overspeed Alert
            |--------------------------------------------------------------------------
            |
            | Sama bentuknya dengan stop_setting (enabled, threshold,
            | notification channel per fitur) supaya konsisten.
            |--------------------------------------------------------------------------
            */

            $table->json('speed_setting')->nullable()->after('stop_setting');

            /*
            |--------------------------------------------------------------------------
            | Offline Alert Dedup
            |--------------------------------------------------------------------------
            |
            | Menandai notifikasi "device offline" sudah dikirim untuk
            | periode offline saat ini, supaya job berkala tidak
            | mengirim notifikasi berulang setiap kali dijalankan.
            | Direset (null) begitu heartbeat baru masuk lagi.
            |--------------------------------------------------------------------------
            */

            $table->timestamp('offline_notified_at')->nullable()->after('last_heartbeat');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {

            $table->dropColumn([
                'last_battery',
                'speed_setting',
                'offline_notified_at',
            ]);
        });
    }
};
