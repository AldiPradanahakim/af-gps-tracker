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
        Schema::table('notifications', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Composite Index (device_id, created_at)
            |--------------------------------------------------------------------------
            |
            | HomeService memuat notifikasi terbaru per device dengan
            | "latest of many" (window function ORDER BY created_at
            | PARTITION BY device_id). Index device_id sendiri sudah ada,
            | tapi query ini butuh index gabungan supaya tidak full scan
            | + sort saat tabel notifications makin besar.
            |--------------------------------------------------------------------------
            */

            $table->index([
                'device_id',
                'created_at',
            ], 'notifications_device_id_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {

            $table->dropIndex('notifications_device_id_created_at_index');
        });
    }
};
