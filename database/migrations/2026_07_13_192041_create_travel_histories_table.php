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
        Schema::create('travel_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('device_log_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('device_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->json('location');

            $table->string('search_address', 255);

            $table->timestamp('received_at');

            $table->timestamps();

            $table->index('device_id');
            $table->index('received_at');
            $table->index([
                'device_id',
                'received_at',
            ]);
            $table->fullText('search_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_histories');
    }
};
