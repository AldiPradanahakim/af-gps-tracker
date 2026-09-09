<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone', 'timezone', 'password', 'is_admin'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable;

    /**
     * Zona waktu yang boleh dipilih pengguna, beserta labelnya.
     *
     * Dibatasi ke tiga zona waktu Indonesia (bukan seluruh daftar IANA)
     * supaya pilihannya sederhana dan tidak ada nilai aneh yang lolos ke
     * database - lihat User::displayTimezone().
     */
    public const TIMEZONE_OPTIONS = [
        'Asia/Jakarta' => 'WIB - Waktu Indonesia Barat (Sumatera, Jawa, Kalimantan Barat & Tengah)',
        'Asia/Makassar' => 'WITA - Waktu Indonesia Tengah (Bali, NTB, NTT, Sulawesi, Kalimantan Selatan, Timur & Utara)',
        'Asia/Jayapura' => 'WIT - Waktu Indonesia Timur (Maluku, Papua)',
    ];

    public const SUPPORTED_TIMEZONES = [
        'Asia/Jakarta',
        'Asia/Makassar',
        'Asia/Jayapura',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * --------------------------------------------------------------------------
     * Zona Waktu Tampilan
     * --------------------------------------------------------------------------
     *
     * Zona waktu yang dipakai untuk MENAMPILKAN tanggal/jam kepada
     * pengguna ini - halaman web, export PDF, dan notifikasi Email/
     * WhatsApp. Penyimpanan timestamp tidak terpengaruh.
     *
     * NULL berarti mengikuti bawaan aplikasi, sehingga akun yang belum
     * pernah memilih zona waktu tetap melihat WIB seperti sebelumnya.
     */
    public function displayTimezone(): string
    {
        $timezone = $this->timezone;

        if (! $timezone || ! in_array($timezone, self::SUPPORTED_TIMEZONES, true)) {

            return (string) config('app.display_timezone', 'Asia/Jakarta');
        }

        return $timezone;
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }
}
