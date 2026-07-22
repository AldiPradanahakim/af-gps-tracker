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
        Schema::create('devices', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('device_id', 50)->unique();

            $table->string('device_password', 255);

            $table->json('home_location')->nullable();

            $table->json('stop_setting')->nullable();

            $table->json('notification_setting')->nullable();

            $table->boolean('is_active')->default(false);

            $table->timestamp('last_heartbeat')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Current Geofence State
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_inside_geofence')
                ->default(false);

            $table->timestamp('activated_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
