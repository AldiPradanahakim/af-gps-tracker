<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivateDeviceRequest;
use App\Services\ActivateDeviceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ActivateDeviceController extends Controller
{
    public function __construct(
        private ActivateDeviceService $deviceService
    ) {}

    /**
     * Halaman aktivasi perangkat untuk CALON pengguna (belum punya akun).
     *
     * Pengguna yang sudah login tidak boleh masuk ke sini: alur ini
     * berlanjut ke "Lengkapi Profil" yang membuat akun BARU. Dia
     * diarahkan ke halaman Utama, tempat popup aktivasi perangkat
     * terbuka otomatis (lihat HomeService::index()).
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {

            return redirect()->route('home');
        }

        return view('devices.create');
    }

    public function store(StoreActivateDeviceRequest $request): RedirectResponse
    {
        if (Auth::check()) {

            return redirect()->route('home');
        }

        $this->deviceService->activateDevice(
            $request->validated()
        );

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Perangkat berhasil diverifikasi. Silakan lengkapi informasi profil.');
    }
}
