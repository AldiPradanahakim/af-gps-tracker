<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Kolom `type` dibuat sebagai CHECK constraint (bukan native enum)
     * oleh grammar PostgreSQL Laravel - lihat migration
     * add_geofence_enter_to_notifications_type untuk pola yang sama.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'pgsql') {

            DB::statement('ALTER TABLE notifications DROP CONSTRAINT IF EXISTS notifications_type_check');

            DB::statement("ALTER TABLE notifications ADD CONSTRAINT notifications_type_check CHECK (type IN ('geofence_enter', 'geofence_exit', 'stop', 'device_online', 'device_offline', 'overspeed', 'low_battery'))");

            return;
        }

        Schema::table('notifications', function ($table) {
            $table->enum('type', [
                'geofence_enter',
                'geofence_exit',
                'stop',
                'device_online',
                'device_offline',
                'overspeed',
                'low_battery',
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'pgsql') {

            DB::statement('ALTER TABLE notifications DROP CONSTRAINT IF EXISTS notifications_type_check');

            DB::statement("ALTER TABLE notifications ADD CONSTRAINT notifications_type_check CHECK (type IN ('geofence_enter', 'geofence_exit', 'stop', 'device_online', 'device_offline'))");

            return;
        }

        Schema::table('notifications', function ($table) {
            $table->enum('type', [
                'geofence_enter',
                'geofence_exit',
                'stop',
                'device_online',
                'device_offline',
            ])->change();
        });
    }
};
