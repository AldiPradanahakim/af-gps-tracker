<?php

namespace App\Http\Controllers;

use App\Services\HomeLocationService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    public function __construct(
        protected HomeLocationService $service
    ) {}

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
                'q' => $query,
                'format' => 'jsonv2',
                'addressdetails' => 1,
                'limit' => 8,
            ]
        );

        return response()->json(
            $response->json()
        );
    }

    public function reverse(Request $request): JsonResponse
    {
        $response = Http::withHeaders([
            'User-Agent' => config('app.name'),
        ])->get(
            'https://nominatim.openstreetmap.org/reverse',
            [
                'lat' => $request->latitude,
                'lon' => $request->longitude,
                'format' => 'jsonv2',
                'addressdetails' => 1,
            ]
        );

        return response()->json(
            $response->json()
        );
    }

    public function save(Request $request): JsonResponse
    {
        $data = $request->validate([
            'device_id'    => ['required'],
            'latitude'     => ['required', 'numeric'],
            'longitude'    => ['required', 'numeric'],
            'display_name' => ['required'],
        ]);

        $device = $this->service->save($data);

        if ($device instanceof Collection) {

            return response()->json([
                'success' => true,
                'message' => 'Home Location berhasil disimpan.',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Home Location berhasil disimpan.',
            'device' => [
                'id' => $device->id,
                'device_id' => $device->device_id,
                'home_location' => $device->home_location,
            ],
        ]);
    }
}
