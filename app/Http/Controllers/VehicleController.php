<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Services\VehicleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function __construct(
        protected VehicleService $vehicleService
    ) {}

    /**
     * Menampilkan halaman informasi kendaraan
     */
    public function create(): View
    {
        return view('vehicles.create');
    }

    /**
     * Menyimpan informasi kendaraan
     */
    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $this->vehicleService->store(
            $request->validated()
        );

        return redirect()
            ->route('home')
            ->with(
                'success',
                'Informasi kendaraan berhasil disimpan.'
            );
    }
}
