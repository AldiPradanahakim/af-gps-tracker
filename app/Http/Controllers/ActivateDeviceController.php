<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivateDeviceRequest;
use App\Services\ActivateDeviceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ActivateDeviceController extends Controller
{
    public function __construct(
        private ActivateDeviceService $deviceService
    ) {}

    public function create(): View
    {
        return view('devices.create');
    }

    public function store(StoreActivateDeviceRequest $request): RedirectResponse
    {
        $this->deviceService->activateDevice(
            $request->validated()
        );

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Perangkat berhasil diverifikasi. Silakan lengkapi informasi profil.');
    }
}
