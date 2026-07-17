<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeviceRequest;
use App\Services\DeviceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DeviceController extends Controller
{
    public function __construct(
        private DeviceService $deviceService
    ) {}

    public function create(): View
    {
        return view('devices.create');
    }

    public function store(StoreDeviceRequest $request): RedirectResponse
    {
        $this->deviceService->activateDevice(
            $request->validated()
        );

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Perangkat berhasil diverifikasi. Silakan lengkapi informasi profil.');
    }
}
