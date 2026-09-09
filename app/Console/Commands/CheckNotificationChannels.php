<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Throwable;

class CheckNotificationChannels extends Command
{
    /**
     * --------------------------------------------------------------------------
     * Diagnosa Kanal Notifikasi (Email + WhatsApp)
     * --------------------------------------------------------------------------
     *
     * Notifikasi Email/WhatsApp dikirim lewat queued job (BR-006, BR-007),
     * jadi kegagalannya tidak pernah muncul di layar - hanya mendarat di
     * tabel failed_jobs. Perintah ini memeriksa SETIAP mata rantai yang
     * bisa membuat notifikasi tidak sampai, lalu melaporkannya sekaligus:
     *
     *   1. Kredensial SMTP benar-benar diterima server (login sungguhan)
     *   2. Token Fonnte masih valid
     *   3. Queue worker berjalan / ada job menumpuk & gagal
     *   4. Ada perangkat yang kanal notifikasinya benar-benar dinyalakan
     *
     * Contoh:
     *   php artisan notifications:check
     *   php artisan notifications:check --email=tujuan@gmail.com
     */
    protected $signature = 'notifications:check
                            {--email= : Kirim email percobaan ke alamat ini}
                            {--phone= : Kirim WhatsApp percobaan ke nomor ini (08xx / 62xx)}';

    protected $description = 'Periksa kesiapan kanal notifikasi Email (SMTP) dan WhatsApp (Fonnte).';

    public function handle(): int
    {
        $this->newLine();
        $this->line('  <options=bold>Diagnosa Kanal Notifikasi - ' . config('app.name') . '</>');
        $this->newLine();

        $failures = 0;

        $failures += $this->checkSmtp() ? 0 : 1;
        $failures += $this->checkFonnte() ? 0 : 1;

        $this->checkQueue();
        $this->checkDeviceSettings();

        $this->newLine();

        if ($failures > 0) {

            $this->error("  {$failures} kanal belum siap - notifikasi tidak akan terkirim sampai diperbaiki.");

            return self::FAILURE;
        }

        $this->info('  Semua kanal siap.');

        return self::SUCCESS;
    }

    /**
     * Uji kredensial SMTP dengan benar-benar login ke server.
     *
     * Sekadar mengecek isi .env tidak cukup: penyebab paling sering
     * notifikasi email tidak terkirim adalah App Password Gmail yang
     * dicabut/kedaluwarsa, dan itu hanya terlihat saat AUTH dijalankan.
     */
    protected function checkSmtp(): bool
    {
        $this->line('  <options=bold>[1] Email (SMTP)</>');

        $host = config('mail.mailers.smtp.host');
        $port = (int) config('mail.mailers.smtp.port');
        $username = config('mail.mailers.smtp.username');
        $password = config('mail.mailers.smtp.password');

        $this->line("      Host     : {$host}:{$port}");
        $this->line('      Username : ' . ($username ?: '(kosong)'));
        $this->line('      Pengirim : ' . config('mail.from.address') . ' (' . config('mail.from.name') . ')');

        if (! $username || ! $password) {

            $this->error('      GAGAL    : MAIL_USERNAME / MAIL_PASSWORD belum diisi.');

            return false;
        }

        try {

            $authenticated = $this->smtpLogin($host, $port, $username, $password);

        } catch (Throwable $exception) {

            $this->error('      GAGAL    : ' . $exception->getMessage());

            return false;
        }

        if (! $authenticated) {

            $this->error('      GAGAL    : Server menolak username/password.');
            $this->newLine();
            $this->warn('      Gmail menolak kata sandi akun biasa. Yang dibutuhkan adalah');
            $this->warn('      App Password 16 karakter:');
            $this->line('        1. Nyalakan Verifikasi 2 Langkah di akun Google pengirim');
            $this->line('           (https://myaccount.google.com/signinoptions/twosv)');
            $this->line('        2. Buat App Password di https://myaccount.google.com/apppasswords');
            $this->line('        3. Salin 16 karakter itu ke MAIL_PASSWORD (tanpa spasi)');
            $this->line('        4. php artisan config:clear');

            return false;
        }

        $this->info('      OK       : Kredensial SMTP diterima server.');

        if ($to = $this->option('email')) {

            try {

                Mail::raw(
                    'Uji coba pengiriman email dari ' . config('app.name') . '. '
                        . 'Kalau pesan ini sampai, kanal Email sudah berfungsi.',
                    fn ($message) => $message
                        ->to($to)
                        ->subject('Uji Notifikasi - ' . config('app.name'))
                );

                $this->info("      OK       : Email percobaan terkirim ke {$to}.");

            } catch (Throwable $exception) {

                $this->error('      GAGAL    : ' . $exception->getMessage());

                return false;
            }
        }

        $this->newLine();

        return true;
    }

    /**
     * Login SMTP manual (EHLO -> STARTTLS -> AUTH LOGIN).
     *
     * Dilakukan langsung di level soket supaya pesan penolakan asli dari
     * server ikut terbaca, bukan tertelan exception generik dari mailer.
     */
    protected function smtpLogin(
        string $host,
        int $port,
        string $username,
        string $password
    ): bool {

        $connection = @stream_socket_client(
            "tcp://{$host}:{$port}",
            $errorCode,
            $errorMessage,
            10
        );

        if (! $connection) {

            throw new \RuntimeException(
                "Tidak bisa terhubung ke {$host}:{$port} ({$errorMessage})."
            );
        }

        try {

            $read = function () use ($connection): string {

                $output = '';

                while ($line = fgets($connection, 512)) {

                    $output .= $line;

                    if (preg_match('/^\d{3} /', $line)) {
                        break;
                    }
                }

                return $output;
            };

            $write = function (string $command) use ($connection): void {
                fwrite($connection, $command . "\r\n");
            };

            $read();

            $write('EHLO localhost');
            $read();

            $write('STARTTLS');
            $read();

            stream_socket_enable_crypto(
                $connection,
                true,
                STREAM_CRYPTO_METHOD_TLS_CLIENT
            );

            $write('EHLO localhost');
            $read();

            $write('AUTH LOGIN');
            $read();

            $write(base64_encode($username));
            $read();

            $write(base64_encode($password));
            $response = $read();

            return str_starts_with(trim($response), '235');

        } finally {

            @fclose($connection);
        }
    }

    /**
     * Uji token Fonnte lewat endpoint /validate.
     */
    protected function checkFonnte(): bool
    {
        $this->line('  <options=bold>[2] WhatsApp (Fonnte)</>');

        $token = config('services.fonnte.token');

        if (! $token) {

            $this->error('      GAGAL    : FONNTE_TOKEN belum diisi di .env.');
            $this->newLine();

            return false;
        }

        $this->line('      Token    : ' . substr($token, 0, 4) . str_repeat('*', max(strlen($token) - 4, 0)));

        try {

            $response = Http::asForm()
                ->withHeaders(['Authorization' => $token])
                ->timeout(15)
                ->post('https://api.fonnte.com/validate', [
                    'target' => '6281234567890',
                ]);

            $body = $response->json();

        } catch (Throwable $exception) {

            $this->error('      GAGAL    : ' . $exception->getMessage());
            $this->newLine();

            return false;
        }

        $reason = data_get($body, 'reason');

        if (data_get($body, 'status') === false && $reason === 'token invalid') {

            $this->error('      GAGAL    : Token Fonnte tidak valid / sudah kedaluwarsa.');
            $this->newLine();
            $this->warn('      Ambil token baru di https://md.fonnte.com/ (menu Device ->');
            $this->warn('      Token), lalu isi FONNTE_TOKEN di .env dan jalankan');
            $this->warn('      php artisan config:clear');
            $this->newLine();

            return false;
        }

        $this->info('      OK       : Token Fonnte diterima.');

        /*
        |--------------------------------------------------------------------------
        | Status perangkat, kuota, dan masa aktif
        |--------------------------------------------------------------------------
        |
        | Token yang valid TIDAK menjamin pesan sampai. Kalau nomor
        | WhatsApp-nya sedang tidak tertaut (device_status "disconnect"),
        | Fonnte tetap menerima request dengan status sukses tapi pesannya
        | tidak pernah terkirim - kegagalan paling sulit disadari, karena
        | tidak ada job yang gagal sama sekali.
        |
        */

        $this->inspectFonnteDevice($token);

        if ($phone = $this->option('phone')) {

            $normalized = preg_replace('/\D/', '', $phone);

            $normalized = str_starts_with($normalized, '0')
                ? '62' . substr($normalized, 1)
                : $normalized;

            $send = Http::asForm()
                ->withHeaders(['Authorization' => $token])
                ->timeout(20)
                ->post(config('services.fonnte.url'), [
                    'target' => $normalized,
                    'message' => 'Uji coba notifikasi dari ' . config('app.name') . '.',
                ]);

            if (data_get($send->json(), 'status') === false) {

                $this->error('      GAGAL    : ' . data_get($send->json(), 'reason', 'tidak diketahui'));
                $this->newLine();

                return false;
            }

            $this->info("      OK       : WhatsApp percobaan terkirim ke {$normalized}.");
        }

        $this->newLine();

        return true;
    }

    /**
     * Ambil detail perangkat Fonnte: status tautan, sisa kuota, dan
     * tanggal kedaluwarsa paket.
     */
    protected function inspectFonnteDevice(string $token): void
    {
        try {

            $device = Http::asForm()
                ->withHeaders(['Authorization' => $token])
                ->timeout(15)
                ->post('https://api.fonnte.com/device')
                ->json();

        } catch (Throwable $exception) {

            $this->warn('      Catatan  : gagal membaca detail perangkat (' . $exception->getMessage() . ').');

            return;
        }

        if (! is_array($device)) {

            return;
        }

        $number = data_get($device, 'device');

        $status = data_get($device, 'device_status');

        $quota = data_get($device, 'quota');

        $expired = data_get($device, 'expired');

        $this->line('      Nomor    : ' . ($number ?: '-'));

        $this->line('      Paket    : ' . (data_get($device, 'package') ?: '-')
            . ' | Sisa kuota: ' . ($quota ?? '-')
            . ' | Aktif s/d: ' . ($expired ?: '-'));

        if ($status === 'connect') {

            $this->info('      OK       : Perangkat WhatsApp tertaut.');

        } else {

            $this->error('      GAGAL    : Perangkat WhatsApp TIDAK tertaut (status: ' . ($status ?: 'tidak diketahui') . ').');
            $this->newLine();
            $this->warn('      Fonnte akan tetap menerima request dan job antrean tetap');
            $this->warn('      "sukses", tapi pesannya TIDAK pernah sampai. Buka');
            $this->warn('      https://md.fonnte.com/ lalu sambungkan ulang nomor');
            $this->warn('      WhatsApp-nya (scan QR).');
            $this->newLine();
        }

        if (is_numeric($quota) && (int) $quota <= 50) {

            $this->warn('      Catatan  : sisa kuota tinggal ' . $quota . ' pesan.');
        }

        if ($expired) {

            try {

                $days = (int) now()->diffInDays(
                    \Illuminate\Support\Carbon::parse($expired),
                    false
                );

                if ($days <= 14) {

                    $this->warn("      Catatan  : paket habis dalam {$days} hari ({$expired}).");
                }

            } catch (Throwable) {

                // Format tanggal tak terduga - abaikan, sudah dicetak di atas.
            }
        }
    }

    /**
     * Laporkan kondisi queue - job Email/WhatsApp hanya benar-benar
     * terkirim kalau ada worker yang memprosesnya.
     */
    protected function checkQueue(): void
    {
        $this->line('  <options=bold>[3] Queue</>');

        $this->line('      Koneksi  : ' . config('queue.default'));

        if (config('queue.default') === 'sync') {

            $this->warn('      Catatan  : mode sync - notifikasi dikirim langsung, tanpa worker.');
            $this->newLine();

            return;
        }

        try {

            $pending = DB::table('jobs')->count();

            $failed = DB::table('failed_jobs')->count();

        } catch (Throwable $exception) {

            $this->warn('      Catatan  : tidak bisa membaca tabel queue (' . $exception->getMessage() . ').');
            $this->newLine();

            return;
        }

        $this->line("      Antre    : {$pending} job");
        $this->line("      Gagal    : {$failed} job");

        if ($pending > 0) {

            $this->warn('      Catatan  : ada job menumpuk. Pastikan "php artisan queue:work" berjalan.');
        }

        if ($failed > 0) {

            $this->warn('      Catatan  : lihat sebabnya dengan "php artisan queue:failed".');
        }

        $this->newLine();
    }

    /**
     * Cek apakah ada perangkat yang kanal notifikasinya memang dinyalakan -
     * kanal yang mati membuat notifikasi tidak pernah di-dispatch sama
     * sekali, dan itu mudah disalahartikan sebagai "notifikasi rusak".
     */
    protected function checkDeviceSettings(): void
    {
        $this->line('  <options=bold>[4] Pengaturan Perangkat</>');

        $devices = Device::query()
            ->whereNotNull('user_id')
            ->with('user')
            ->get();

        if ($devices->isEmpty()) {

            $this->warn('      Catatan  : belum ada perangkat yang terhubung ke akun pengguna.');

            return;
        }

        foreach ($devices as $device) {

            /** @var User|null $user */
            $user = $device->user;

            $email = (bool) data_get($device, 'notification_setting.email', false);

            $whatsapp = (bool) data_get($device, 'notification_setting.whatsapp', false);

            $stopEmail = (bool) data_get($device, 'stop_setting.email_notification', false);

            $stopWhatsapp = (bool) data_get($device, 'stop_setting.whatsapp_notification', false);

            $this->line("      {$device->device_id} ({$user?->name})");

            $this->line(
                '        Umum  - Email: ' . ($email ? 'aktif' : 'nonaktif')
                    . ' | WhatsApp: ' . ($whatsapp ? 'aktif' : 'nonaktif')
            );

            $this->line(
                '        Stop  - Email: ' . ($stopEmail ? 'aktif' : 'nonaktif')
                    . ' | WhatsApp: ' . ($stopWhatsapp ? 'aktif' : 'nonaktif')
            );

            if (! $user?->email) {

                $this->warn('        Peringatan: akun tidak punya email tujuan.');
            }

            if (($whatsapp || $stopWhatsapp) && ! $user?->phone) {

                $this->warn('        Peringatan: WhatsApp aktif tapi akun tidak punya nomor telepon.');
            }
        }
    }
}
