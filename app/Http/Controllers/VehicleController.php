<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Services\VehicleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function __construct(protected VehicleService $vehicleService) {}

    public function create(): View
    {
        return view('vehicles.create');
    }

    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $device = $request->user()->devices()->first();

        $this->vehicleService->create(
            $device,
            $request->validated()
        );

        return redirect()
            ->route('dashboard')
            ->with('success', 'Kendaraan berhasil ditambahkan.');
    }
}
