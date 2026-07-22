<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\Home\StoreDeviceRequest;
use App\Services\Home\DeviceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class DeviceController extends Controller
{
    public function __construct(
        protected DeviceService $deviceService
    ) {}

    /**
     * Aktivasi device baru dari halaman Home.
     */
    public function store(StoreDeviceRequest $request): JsonResponse
    {
        try {

            $device = $this->deviceService->activate(
                $request->validated()
            );

            return response()->json([

                'success' => true,

                'message' => 'Perangkat berhasil diaktivasi.',

                'device' => [

                    'id' => $device->id,

                    'device_id' => $device->device_id,

                ],

            ]);
        } catch (ValidationException $exception) {

            return response()->json([

                'success' => false,

                'errors' => $exception->errors(),

            ], 422);
        } catch (\Throwable $exception) {

            report($exception);

            return response()->json([

                'success' => false,

                'message' => 'Terjadi kesalahan pada server.',

            ], 500);
        }
    }
}
