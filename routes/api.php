<?php

use App\Http\Controllers\AdministrativeAreaController;
use App\Http\Controllers\GeofenceController;
use App\Http\Controllers\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Administrative Area
|--------------------------------------------------------------------------
|
| Endpoint publik hanya untuk membaca data wilayah.
|
*/

Route::prefix('administrative')
    ->name('api.administrative.')
    ->group(function () {

        Route::get('/provinces', [
            AdministrativeAreaController::class,
            'provinces',
        ])->name('provinces');

        Route::get('/regencies', [
            AdministrativeAreaController::class,
            'regencies',
        ])->name('regencies');

        Route::get('/city', [
            AdministrativeAreaController::class,
            'city',
        ])->name('city');

        Route::get('/districts', [
            AdministrativeAreaController::class,
            'districts',
        ])->name('districts');

        Route::get('/districts/{districtCode}', [
            AdministrativeAreaController::class,
            'district',
        ])->name('district');

        Route::get('/districts/{districtCode}/villages', [
            AdministrativeAreaController::class,
            'villages',
        ])->name('villages');

        Route::get('/villages/{villageCode}', [
            AdministrativeAreaController::class,
            'village',
        ])->name('village');

        Route::get('/polygon/{level}/{code}', [
            AdministrativeAreaController::class,
            'polygon',
        ])->name('polygon');

        Route::get('/geojson/{level}/{code}', [
            AdministrativeAreaController::class,
            'geoJson',
        ])->name('geojson')->middleware('throttle:20,1');

        Route::get('/geojson/{level}', [
            AdministrativeAreaController::class,
            'allGeoJson',
        ])->name('geojson.all')->middleware('throttle:20,1');
    });

/*
|--------------------------------------------------------------------------
| Authenticated API
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/search', SearchController::class)
        ->name('api.search');

    Route::get('/geofences', [GeofenceController::class, 'index'])
        ->name('api.geofences.index');

    Route::patch('/geofences/{geofence}/status', [
        GeofenceController::class,
        'updateStatus'
    ])->name('api.geofences.status');

    Route::delete('/geofences/{geofence}', [
        GeofenceController::class,
        'destroy'
    ])->name('api.geofences.destroy');
});
