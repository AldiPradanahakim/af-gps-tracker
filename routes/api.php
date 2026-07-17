<?php

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

Route::middleware('auth:sanctum')->group(function () {

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
