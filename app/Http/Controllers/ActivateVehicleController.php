<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivateVehicleRequest;
use App\Services\ActivateVehicleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ActivateVehicleController extends Controller
{
    public function __construct(
        protected ActivateVehicleService $vehicleService
    ) {}

    /**
     * Halaman informasi kendaraan.
     */
    public function create(): View
    {
        return view('vehicles.create');
    }

    /**
     * Simpan kendaraan.
     */
    public function store(
        StoreActivateVehicleRequest $request
    ): RedirectResponse {

        $this->vehicleService->store(
            $request->validated()
        );

        return redirect()
            ->route('home')
            ->with(
                'success',
                'Aktivasi perangkat berhasil diselesaikan.'
            );
    }
}
