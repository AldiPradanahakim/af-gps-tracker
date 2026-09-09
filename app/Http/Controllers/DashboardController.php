<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): RedirectResponse
    {
        $user = Auth::user();
        assert($user instanceof User);

        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | Pengguna yang sudah punya akun SELALU diarahkan ke halaman Utama
        |--------------------------------------------------------------------------
        |
        | Sebelumnya, pengguna tanpa perangkat dilempar ke halaman
        | aktivasi perangkat versi tamu (devices.create) - yang setelah
        | aktivasi berhasil justru melanjutkan ke "Lengkapi Profil",
        | seolah-olah dia mendaftar dari nol. Padahal ini terjadi juga
        | pada pengguna lama yang baru saja menghapus kendaraan
        | terakhirnya: akun dan profilnya sudah ada, yang belum ada hanya
        | perangkat/kendaraannya.
        |
        | Sekarang dia langsung dibawa ke halaman Utama, dan HomeService
        | menentukan popup mana yang otomatis terbuka: "Aktivasi
        | Perangkat" (lalu berlanjut ke "Informasi Kendaraan") atau
        | langsung "Informasi Kendaraan" - tanpa langkah profil sama
        | sekali.
        |
        */

        return redirect()->route('home');
    }
}
