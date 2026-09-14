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
        Schema::table('geofences', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Status Masuk/Keluar per Geofence
            |--------------------------------------------------------------------------
            |
            | Sebelumnya status hanya ada satu per device
            | (devices.is_inside_geofence), sehingga kendaraan yang keluar
            | dari Radius tetapi masih di dalam Administratif tidak bisa
            | dibedakan - dan hanya satu notifikasi yang pernah terkirim.
            |
            | NULL = belum pernah dievaluasi. Ini disengaja: kalau
            | defaultnya false, payload GPS pertama setelah migrasi akan
            | dianggap "baru masuk" untuk kendaraan yang sebenarnya memang
            | sudah di dalam area, dan mengirim notifikasi palsu. Evaluasi
            | pertama hanya menetapkan baseline, tanpa notifikasi.
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_inside')
                ->nullable()
                ->after('status');

            /*
            |--------------------------------------------------------------------------
            | Pengingat "Masih di Luar"
            |--------------------------------------------------------------------------
            |
            | Waktu notifikasi keluar terakhir dikirim untuk periode di luar
            | area yang sedang berjalan. Dipakai untuk menghitung jeda
            | pengingat berikutnya, dan direset ke NULL begitu kendaraan
            | masuk kembali.
            |--------------------------------------------------------------------------
            */

            $table->timestamp('last_exit_notified_at')
                ->nullable()
                ->after('is_inside');

            /*
            |--------------------------------------------------------------------------
            | Waktu perpindahan status terakhir, dipakai untuk menghitung
            | durasi "berada di dalam/di luar" pada riwayat geofence.
            |--------------------------------------------------------------------------
            */

            $table->timestamp('state_changed_at')
                ->nullable()
                ->after('last_exit_notified_at');
        });

        /*
        |--------------------------------------------------------------------------
        | TIDAK ada pengisian data awal - ini disengaja.
        |--------------------------------------------------------------------------
        |
        | Godaannya adalah menyalin devices.is_inside_geofence ke tiap
        | geofence aktif. Itu justru berbahaya: kode lama hanya pernah
        | mengevaluasi SATU geofence (yang pertama), jadi nilai kolom itu
        | tidak mewakili geofence kedua dan ketiga sama sekali - dan untuk
        | device yang belum pernah dievaluasi, isinya cuma nilai bawaan
        | kolom (false).
        |
        | Menyalinnya akan membuat, misalnya, geofence Poligon "Kantor"
        | yang jaraknya puluhan kilometer ditandai "di dalam area", lalu
        | payload GPS pertama setelah deploy mengirim notifikasi "Keluar
        | Geofence" palsu - lengkap dengan email, WhatsApp, baris riwayat,
        | dan pengingat berulang yang tidak akan pernah berhenti karena
        | kendaraannya memang tidak pernah ada di sana.
        |
        | Membiarkan seluruh baris NULL justru memakai mekanisme baseline
        | yang sudah dirancang di atas: satu payload GPS pertama dipakai
        | untuk menetapkan status apa adanya, tanpa notifikasi apa pun.
        |--------------------------------------------------------------------------
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('geofences', function (Blueprint $table) {

            $table->dropColumn([
                'is_inside',
                'last_exit_notified_at',
                'state_changed_at',
            ]);
        });
    }
};
