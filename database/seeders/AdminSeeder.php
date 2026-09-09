<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * --------------------------------------------------------------------------
     * Kredensial Akun Admin
     * --------------------------------------------------------------------------
     *
     * JANGAN menuliskan email/kata sandi asli di file ini. Repositori ini
     * publik, sehingga apa pun yang ditulis di sini bisa dibaca siapa
     * saja - dan begitu seeder ini dijalankan di server produksi, nilai
     * itu benar-benar bisa dipakai untuk masuk sebagai admin.
     *
     * Nilainya diambil dari .env (yang tidak ikut ke git). Kalau tidak
     * diisi, seeder memakai kata sandi acak dan mencetaknya sekali ke
     * layar - aman secara bawaan.
     *
     *   ADMIN_EMAIL=...
     *   ADMIN_PASSWORD=...
     *
     * Aman dijalankan berulang kali: memakai firstOrCreate berdasarkan
     * email, jadi tidak akan menimpa kata sandi admin yang sudah pernah
     * diubah lewat aplikasi.
     *
     * Sengaja TIDAK dipanggil dari DatabaseSeeder (yang hanya jalan di
     * environment local) - ini satu-satunya jalur untuk membuat akun
     * admin pertama di produksi, jadi harus bisa dijalankan di sana.
     * Jalankan sekali secara manual setelah deploy pertama:
     *
     *   php artisan db:seed --class=AdminSeeder
     */
    public function run(): void
    {
        $email = (string) env('ADMIN_EMAIL', 'admin@af-gps-tracker.test');

        $password = (string) env(
            'ADMIN_PASSWORD',
            Str::password(16)
        );

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Administrator',
                'password' => Hash::make($password),
                'is_admin' => true,
            ]
        );

        if (! $user->wasRecentlyCreated) {
            $this->command?->newLine();
            $this->command?->warn("Akun admin dengan email {$email} sudah ada - tidak ada perubahan.");

            return;
        }

        $this->command?->newLine();
        $this->command?->info('Akun admin dibuat:');
        $this->command?->line('  Email      : ' . $email);
        $this->command?->line('  Kata sandi : ' . $password);
        $this->command?->newLine();
        $this->command?->warn('Catat sekarang - nilainya tidak disimpan di mana pun. Segera ganti lewat halaman profil admin.');
    }
}
