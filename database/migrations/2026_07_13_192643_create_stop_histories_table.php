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
        Schema::create('stop_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('device_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->json('location');

            $table->string('search_address', 255);

            $table->timestamp('start_time');

            $table->timestamp('end_time')->nullable();

            $table->unsignedInteger('duration_seconds')->default(0);

            $table->boolean('notification_sent')->default(false);

            $table->timestamps();

            $table->index('device_id');
            $table->index('start_time');
            $table->index([
                'device_id',
                'start_time',
            ]);
            $table->fullText('search_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stop_histories');
    }
};
