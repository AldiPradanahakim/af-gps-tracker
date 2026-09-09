<?php

namespace App\Helpers;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AppTime
{
    /**
     * --------------------------------------------------------------------------
     * Zona Waktu Terpusat
     * --------------------------------------------------------------------------
     *
     * Aplikasi ini punya DUA zona waktu yang berbeda peran:
     *
     * - config('app.timezone')          zona penyimpanan (Carbon/DB)
     * - zona tampilan                   zona yang dilihat pengguna
     *
     * Zona TAMPILAN mengikuti pilihan masing-masing pengguna
     * (users.timezone): WIB untuk Jawa/Sumatera, WITA untuk Bali sampai
     * Sulawesi, WIT untuk Maluku/Papua. Kalau pengguna belum memilih -
     * atau konteksnya memang tidak punya pengguna (mis. perintah artisan) -
     * dipakai bawaan config('app.display_timezone').
     *
     * Sebelumnya batas hari dihitung dengan `now('Asia/Jakarta')->utc()`
     * yang mengasumsikan penyimpanan selalu UTC. Ketika APP_TIMEZONE
     * diubah ke Asia/Jakarta (lihat .env), asumsi itu jadi salah dan
     * query "hari ini" bergeser 7 jam - itulah sebabnya Riwayat
     * Perjalanan di kartu Informasi Kendaraan tampil kosong dan
     * timestamp di export PDF meleset.
     *
     * Helper ini menghitung batas hari di zona TAMPILAN lalu
     * mengonversinya ke zona PENYIMPANAN, sehingga benar untuk kombinasi
     * konfigurasi mana pun (konversi ke zona yang sama bersifat no-op).
     */

    /**
     * Zona waktu tampilan (yang dilihat pengguna).
     *
     * Urutan penentuan:
     *
     *   1. $user yang dioper eksplisit - WAJIB dipakai di konteks yang
     *      tidak punya sesi login, terutama queued job notifikasi
     *      Email/WhatsApp: jam di pesan harus mengikuti zona waktu
     *      PENERIMA, bukan zona waktu server.
     *   2. Pengguna yang sedang login (request web biasa).
     *   3. Bawaan aplikasi (WIB).
     */
    public static function displayTimezone(?User $user = null): string
    {
        if (! $user) {

            $authenticated = Auth::user();

            $user = $authenticated instanceof User
                ? $authenticated
                : null;
        }

        if ($user) {

            return $user->displayTimezone();
        }

        return (string) config('app.display_timezone', 'Asia/Jakarta');
    }

    /**
     * Zona waktu penyimpanan (yang dipakai Carbon & kolom timestamp).
     */
    public static function storageTimezone(): string
    {
        return (string) config('app.timezone', 'UTC');
    }

    /**
     * Label singkat zona waktu tampilan (WIB/WITA/WIT).
     */
    public static function timezoneLabel(?User $user = null): string
    {
        $timezone = self::displayTimezone($user);

        return match ($timezone) {
            'Asia/Jakarta' => 'WIB',
            'Asia/Makassar' => 'WITA',
            'Asia/Jayapura' => 'WIT',
            default => $timezone,
        };
    }

    /**
     * Waktu sekarang di zona tampilan.
     */
    public static function now(?User $user = null): Carbon
    {
        return Carbon::now(self::displayTimezone($user));
    }

    /**
     * Awal hari (di zona tampilan) yang siap dipakai untuk query
     * kolom timestamp.
     */
    public static function startOfDay(
        ?CarbonInterface $reference = null,
        ?User $user = null
    ): Carbon {

        $timezone = self::displayTimezone($user);

        return self::normalize($reference, $timezone)
            ->startOfDay()
            ->timezone(self::storageTimezone());
    }

    /**
     * Akhir hari (di zona tampilan) yang siap dipakai untuk query
     * kolom timestamp.
     */
    public static function endOfDay(
        ?CarbonInterface $reference = null,
        ?User $user = null
    ): Carbon {

        $timezone = self::displayTimezone($user);

        return self::normalize($reference, $timezone)
            ->endOfDay()
            ->timezone(self::storageTimezone());
    }

    /**
     * Ubah nilai tanggal apa pun menjadi Illuminate\Support\Carbon di
     * zona yang diminta.
     *
     * Penting karena pemanggil bisa mengoper Carbon\Carbon polos (mis.
     * hasil \Carbon\Carbon::parse() di Blade) maupun
     * Illuminate\Support\Carbon dari kolom timestamp Eloquent - keduanya
     * CarbonInterface, tapi hanya yang kedua yang cocok dengan tipe
     * kembalian method ini.
     */
    protected static function normalize(
        ?CarbonInterface $dateTime,
        string $timezone
    ): Carbon {

        if (! $dateTime) {

            return Carbon::now($timezone);
        }

        return Carbon::instance($dateTime)->timezone($timezone);
    }

    /**
     * Ubah tanggal/waktu yang diketik pengguna (selalu dibaca sebagai
     * zona tampilan) menjadi Carbon di zona penyimpanan.
     */
    public static function parseInput(
        string $value,
        bool $endOfDay = false,
        ?User $user = null
    ): Carbon {

        $parsed = Carbon::parse($value, self::displayTimezone($user));

        $parsed = $endOfDay
            ? $parsed->endOfDay()
            : $parsed->startOfDay();

        return $parsed->timezone(self::storageTimezone());
    }

    /**
     * Format satu timestamp untuk ditampilkan ke pengguna.
     *
     * Selalu dikonversi ke zona tampilan lebih dulu, apa pun zona
     * penyimpanannya, lalu diberi label zona supaya tidak ambigu.
     */
    public static function format(
        ?CarbonInterface $dateTime,
        string $format = 'd/m/Y H:i:s',
        bool $withLabel = true,
        string $fallback = '-',
        ?User $user = null
    ): string {

        if (! $dateTime) {

            return $fallback;
        }

        $formatted = $dateTime
            ->copy()
            ->timezone(self::displayTimezone($user))
            ->format($format);

        return $withLabel
            ? $formatted . ' ' . self::timezoneLabel($user)
            : $formatted;
    }

    /**
     * Format panjang berbahasa Indonesia (mis. "09 September 2026, 14:05").
     */
    public static function formatLong(
        ?CarbonInterface $dateTime,
        string $format = 'd F Y, H:i',
        bool $withLabel = true,
        string $fallback = '-',
        ?User $user = null
    ): string {

        if (! $dateTime) {

            return $fallback;
        }

        $formatted = $dateTime
            ->copy()
            ->timezone(self::displayTimezone($user))
            ->locale('id')
            ->translatedFormat($format);

        return $withLabel
            ? $formatted . ' ' . self::timezoneLabel($user)
            : $formatted;
    }
}
