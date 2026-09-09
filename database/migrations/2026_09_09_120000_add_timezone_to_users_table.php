<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * --------------------------------------------------------------------------
     * Zona Waktu per Pengguna
     * --------------------------------------------------------------------------
     *
     * Indonesia punya tiga zona waktu (WIB/WITA/WIT). Sebelumnya seluruh
     * tampilan waktu dipaku ke WIB, sehingga pengguna di Makassar atau
     * Jayapura melihat jam yang meleset 1-2 jam dari jam dinding mereka.
     *
     * Kolom ini menyimpan zona waktu pilihan pengguna. NULL berarti
     * "ikut bawaan aplikasi" (config('app.display_timezone')), sehingga
     * akun lama tetap berperilaku persis seperti sebelumnya.
     *
     * Penyimpanan timestamp TIDAK berubah - hanya lapisan tampilan
     * (halaman, export PDF, notifikasi Email/WhatsApp) yang mengonversi.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('timezone', 64)
                ->nullable()
                ->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('timezone');
        });
    }
};
