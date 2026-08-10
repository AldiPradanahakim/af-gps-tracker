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
            | Low Battery State (transition detection)
            |--------------------------------------------------------------------------
            |
            | Sama pola dengan overspeed_active: notifikasi baterai lemah
            | cuma dikirim sekali saat melintasi ambang batas, direset
            | begitu baterai kembali di atas ambang (mis. sudah dicas).
            |--------------------------------------------------------------------------
            */

            $table->boolean('low_battery_active')->default(false)->after('last_battery');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {

            $table->dropColumn('low_battery_active');
        });
    }
};
