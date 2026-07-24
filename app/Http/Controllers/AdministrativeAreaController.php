<?php

namespace App\Http\Controllers;

use App\Services\AdministrativeAreaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Throwable;

class AdministrativeAreaController extends Controller
{
    public function __construct(
        private readonly AdministrativeAreaService $administrativeAreaService
    ) {}

    /**
     * Kota Bandung.
     */
    public function city(): JsonResponse
    {
        try {

            return response()->json([
                'success' => true,
                'data' => $this->administrativeAreaService->getCity(),
            ]);
        } catch (Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Daftar kecamatan.
     */
    public function districts(Request $request): JsonResponse
    {
        try {

            $data = $this->administrativeAreaService
                ->searchDistricts(
                    $request->string('keyword')->toString()
                );

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {

            dd(
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
        }
    }

    /**
     * Detail kecamatan.
     */
    public function district(string $districtCode): JsonResponse
    {
        try {

            return response()->json([
                'success' => true,
                'data' => $this->administrativeAreaService
                    ->getDistrict($districtCode),
            ]);
        } catch (Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Daftar kelurahan berdasarkan kecamatan.
     */
    public function villages(
        Request $request,
        string $districtCode
    ): JsonResponse {
        try {

            return response()->json([
                'success' => true,
                'data' => $this->administrativeAreaService
                    ->searchVillages(
                        $districtCode,
                        $request->string('keyword')->toString()
                    ),
            ]);
        } catch (Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Detail kelurahan.
     */
    public function village(
        string $villageCode
    ): JsonResponse {
        try {

            return response()->json([
                'success' => true,
                'data' => $this->administrativeAreaService
                    ->getVillage($villageCode),
            ]);
        } catch (Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Polygon wilayah.
     */
    public function polygon(
        string $level,
        string $code
    ): JsonResponse {

        try {

            return response()->json([
                'success' => true,
                'data' => $this->administrativeAreaService
                    ->getPolygon($level, $code),
            ]);
        } catch (InvalidArgumentException $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * FeatureCollection satu wilayah.
     */
    public function geoJson(
        string $level,
        string $code
    ): JsonResponse {

        try {

            return response()->json([
                'success' => true,
                'data' => $this->administrativeAreaService
                    ->getGeoJson($level, $code),
            ]);
        } catch (InvalidArgumentException $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Seluruh GeoJSON berdasarkan level.
     */
    public function allGeoJson(
        string $level
    ): JsonResponse {

        try {

            return response()->json([
                'success' => true,
                'data' => $this->administrativeAreaService
                    ->getAllGeoJson($level),
            ]);
        } catch (InvalidArgumentException $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
