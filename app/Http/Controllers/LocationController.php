<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Services\HomeLocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LocationController extends Controller
{
    public function __construct(
        protected HomeLocationService $service
    ) {}

    /**
     * ----------------------------------------------------------
     * Search Lokasi via Nominatim
     * ----------------------------------------------------------
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim(
            $request->get('q', '')
        );

        if ($query === '') {

            return response()->json([]);
        }

        $response = Http::withHeaders([
            'User-Agent' => config('app.name'),
        ])->get(
            'https://nominatim.openstreetmap.org/search',
            [
                'q'             => $query,
                'format'        => 'jsonv2',
                'addressdetails' => 1,
                'limit'         => 8,
            ]
        );

        return response()->json(
            $response->json()
        );
    }

    /**
     * ----------------------------------------------------------
     * Reverse Geocoding via Nominatim
     * ----------------------------------------------------------
     */
    public function reverse(Request $request): JsonResponse
    {
        $response = Http::withHeaders([
            'User-Agent' => config('app.name'),
        ])->get(
            'https://nominatim.openstreetmap.org/reverse',
            [
                'lat'            => $request->latitude,
                'lon'            => $request->longitude,
                'format'         => 'jsonv2',
                'addressdetails' => 1,
            ]
        );

        return response()->json(
            $response->json()
        );
    }

    /**
     * ----------------------------------------------------------
     * Save (Create) Home Location
     *
     * Mendukung dua mode:
     * - device_id = uuid    → simpan ke satu device
     * - device_id = "all"   → simpan lokasi yang sama ke semua
     *                         device milik user yang belum
     *                         memiliki Home Location
     *
     * POST /api/home-location
     * ----------------------------------------------------------
     */
    public function save(Request $request): JsonResponse
    {
        $data = $request->validate([
            'device_id'    => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if ($value !== 'all' && !Str::isUuid($value)) {
                        $fail('Kendaraan tidak valid.');
                    }
                },
            ],
            'latitude'     => ['required', 'numeric'],
            'longitude'    => ['required', 'numeric'],
            'display_name' => ['required', 'string'],
        ]);

        $payload = [
            'latitude'     => $data['latitude'],
            'longitude'    => $data['longitude'],
            'display_name' => $data['display_name'],
        ];

        if ($data['device_id'] === 'all') {

            $devices = $this->service->saveToAll(Auth::id(), $payload);

        } else {

            /*
            |----------------------------------------------------------
            | Pastikan device milik user yang login
            |----------------------------------------------------------
            */
            $device = Device::where('id', $data['device_id'])
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $devices = collect([
                $this->service->save($device->id, $payload),
            ]);

        }

        return response()->json([
            'success' => true,
            'message' => 'Lokasi Rumah berhasil disimpan.',
            'devices' => $devices->map(fn(Device $device) => [
                'id'            => $device->id,
                'device_id'     => $device->device_id,
                'home_location' => $device->home_location,
            ])->values(),
        ]);
    }

    /**
     * ----------------------------------------------------------
     * Delete Home Location
     *
     * DELETE /api/home-location
     * ----------------------------------------------------------
     */
    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'device_id' => ['required', 'string', 'uuid'],
        ]);

        /*
        |----------------------------------------------------------
        | Pastikan device milik user yang login
        |----------------------------------------------------------
        */
        Device::where('id', $data['device_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $device = $this->service->delete(
            $data['device_id']
        );

        return response()->json([
            'success' => true,
            'message' => 'Lokasi Rumah berhasil dihapus.',
            'device'  => [
                'id'            => $device->id,
                'device_id'     => $device->device_id,
                'home_location' => $device->home_location,
            ],
        ]);
    }
}
