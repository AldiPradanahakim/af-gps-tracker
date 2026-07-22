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
        Schema::table('vehicles', function (Blueprint $table) {

            $table->string('marker_icon', 30)
                ->default('car')
                ->after('plate_number');

            $table->string('marker_color', 20)
                ->default('green')
                ->after('marker_icon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {

            $table->dropColumn([
                'marker_icon',
                'marker_color',
            ]);
        });
    }
};
