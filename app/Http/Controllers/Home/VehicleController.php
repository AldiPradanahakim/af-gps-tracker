<?php

namespace App\Http\Controllers\Home;

use Throwable;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Home\StoreVehicleRequest;
use App\Services\Home\VehicleService;

class VehicleController extends Controller
{
    public function __construct(
        protected VehicleService $vehicleService
    ) {}

    /**
     * Simpan informasi kendaraan
     * setelah aktivasi device dari Dashboard Home.
     */
    public function store(
        StoreVehicleRequest $request
    ): JsonResponse {

        try {

            $vehicle = $this->vehicleService->store(

                $request->validated()

            );

            return response()->json([

                'success' => true,

                'message' => 'Informasi kendaraan berhasil disimpan.',

                'vehicle' => $vehicle,

            ]);
        } catch (ValidationException $exception) {

            return response()->json([

                'success' => false,

                'errors' => $exception->errors(),

            ], 422);
        } catch (Throwable $exception) {

            report($exception);

            return response()->json([

                'success' => false,

                'message' => 'Terjadi kesalahan pada server.',

            ], 500);
        }
    }
}
