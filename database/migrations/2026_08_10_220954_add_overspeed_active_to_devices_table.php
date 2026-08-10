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
            | Overspeed State (transition detection)
            |--------------------------------------------------------------------------
            |
            | Sama pola-nya dengan is_inside_geofence: menandai apakah
            | device SEDANG dalam kondisi overspeed, supaya notifikasi
            | cuma dikirim saat melintasi batas (transisi normal -> over),
            | bukan berulang setiap titik selama masih di atas batas.
            |--------------------------------------------------------------------------
            */

            $table->boolean('overspeed_active')->default(false)->after('speed_setting');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {

            $table->dropColumn('overspeed_active');
        });
    }
};
