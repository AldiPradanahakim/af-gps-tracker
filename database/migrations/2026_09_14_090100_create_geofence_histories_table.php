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
        Schema::create('geofence_histories', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('device_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Geofence boleh dihapus tanpa menghilangkan riwayatnya. Karena
            | itu relasinya nullOnDelete dan nama serta tipe geofence ikut
            | disalin (denormalisasi) supaya riwayat tetap terbaca.
            |--------------------------------------------------------------------------
            */

            $table->foreignUuid('geofence_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('geofence_name', 100);

            $table->enum('geofence_type', [
                'radius',
                'administrative',
                'custom',
            ]);

            $table->enum('event', [
                'enter',
                'exit',
            ]);

            $table->json('location');

            $table->string('search_address', 255)->nullable();

            $table->timestamp('occurred_at');

            /*
            |--------------------------------------------------------------------------
            | Lama kendaraan berada pada status SEBELUMNYA sebelum berpindah
            | (mis. pada baris "enter": berapa lama dia di luar area).
            | NULL kalau tidak ada perpindahan sebelumnya yang tercatat.
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('duration_seconds')->nullable();

            $table->timestamps();

            $table->index('device_id');
            $table->index('geofence_id');
            $table->index('event');
            $table->index(['device_id', 'occurred_at']);
            $table->index(['geofence_id', 'occurred_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geofence_histories');
    }
};
