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
|
| Semua endpoint API untuk aplikasi GPS Tracker.
|
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    Route::get('/search', SearchController::class)
        ->name('api.search');

    /*
    |--------------------------------------------------------------------------
    | Administrative Area
    |--------------------------------------------------------------------------
    */

    Route::prefix('administrative')->name('api.administrative.')->group(function () {

        /*
    |----------------------------------------------------------------------
    | City
    |----------------------------------------------------------------------
    */

        Route::get('/city', [
            AdministrativeAreaController::class,
            'city',
        ])->name('city');

        /*
    |----------------------------------------------------------------------
    | District
    |----------------------------------------------------------------------
    */

        Route::get('/districts', [
            AdministrativeAreaController::class,
            'districts',
        ])->name('districts');

        Route::get('/districts/{districtCode}', [
            AdministrativeAreaController::class,
            'district',
        ])->name('district');

        /*
    |----------------------------------------------------------------------
    | Village
    |----------------------------------------------------------------------
    */

        Route::get('/districts/{districtCode}/villages', [
            AdministrativeAreaController::class,
            'villages',
        ])->name('villages');

        Route::get('/villages/{villageCode}', [
            AdministrativeAreaController::class,
            'village',
        ])->name('village');

        /*
    |----------------------------------------------------------------------
    | GeoJSON
    |----------------------------------------------------------------------
    */

        Route::get('/polygon/{level}/{code}', [
            AdministrativeAreaController::class,
            'polygon',
        ])->name('polygon');

        Route::get('/geojson/{level}/{code}', [
            AdministrativeAreaController::class,
            'geoJson',
        ])->name('geojson');

        Route::get('/geojson/{level}', [
            AdministrativeAreaController::class,
            'allGeoJson',
        ])->name('geojson.all');
    });

    /*
    |--------------------------------------------------------------------------
    | Geofence
    |--------------------------------------------------------------------------
    */

    Route::get('/geofences', [GeofenceController::class, 'index'])
        ->name('api.geofences.index');

    Route::patch('/geofences/{geofence}/status', [GeofenceController::class, 'updateStatus'])
        ->name('api.geofences.status');

    Route::delete('/geofences/{geofence}', [GeofenceController::class, 'destroy'])
        ->name('api.geofences.destroy');
});
