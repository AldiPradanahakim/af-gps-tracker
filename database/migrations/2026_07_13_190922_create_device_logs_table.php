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
        Schema::create('device_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('device_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('message_id');

            $table->json('payload');

            $table->enum('status', [
                'valid',
                'invalid',
                'duplicate',
            ])->default('valid');

            $table->timestamp('received_at');

            $table->timestamps();

            $table->unique([
                'device_id',
                'message_id',
            ]);

            $table->index('device_id');
            $table->index('received_at');
            $table->index([
                'device_id',
                'received_at',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_logs');
    }
};
