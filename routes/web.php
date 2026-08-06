<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivateDeviceController;
use App\Http\Controllers\ActivateVehicleController;
use App\Http\Controllers\GeofenceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\Home\DeviceController as HomeDeviceController;
use App\Http\Controllers\Home\VehicleController as HomeVehicleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Vehicle\VehicleController;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Device Activation
|--------------------------------------------------------------------------
*/

Route::get('/devices/activate', [ActivateDeviceController::class, 'create'])
    ->name('devices.create');

Route::post('/devices/activate', [ActivateDeviceController::class, 'store'])
    ->name('devices.store');

/*
|--------------------------------------------------------------------------
| Profile Registration
|--------------------------------------------------------------------------
*/

Route::get('/profile', [ProfileController::class, 'edit'])
    ->name('profile.edit');

Route::post('/profile', [ProfileController::class, 'store'])
    ->name('profile.store');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [
        DashboardController::class,
        'index'
    ])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Home
    |--------------------------------------------------------------------------
    */

    Route::get('/home', [
        HomeController::class,
        'index'
    ])->name('home');

    /*
    |--------------------------------------------------------------------------
    | Home Device
    |--------------------------------------------------------------------------
    */

    Route::post('/home/devices/activate', [
        HomeDeviceController::class,
        'store'
    ])->name('home.devices.activate');

    /*
    |--------------------------------------------------------------------------
    | Home Vehicle
    |--------------------------------------------------------------------------
    */

    Route::post('/home/vehicles', [
        HomeVehicleController::class,
        'store'
    ])->name('home.vehicles.store');

    /*
    |--------------------------------------------------------------------------
    | Vehicle
    |--------------------------------------------------------------------------
    */

    Route::get('/vehicles/create', [
        ActivateVehicleController::class,
        'create'
    ])->name('vehicles.create');

    Route::post('/vehicles', [
        ActivateVehicleController::class,
        'store'
    ])->name('vehicles.store');

    Route::get('/vehicles/{device}', [
        VehicleController::class,
        'show'
    ])->name('vehicles.show');

    Route::delete('/vehicles/{device}', [
        VehicleController::class,
        'destroy'
    ])->name('vehicles.destroy');

    Route::patch('/vehicles/{device}/information', [
        VehicleController::class,
        'updateInformation'
    ])->name('vehicles.information.update');

    Route::patch('/vehicles/{device}/stop-setting', [
        VehicleController::class,
        'updateStopSetting'
    ])->name('vehicles.stop-setting.update');

    Route::patch('/vehicles/{device}/notification-setting', [
        VehicleController::class,
        'updateNotificationSetting'
    ])->name('vehicles.notification-setting.update');

    Route::get('/geofences', [
        GeofenceController::class,
        'all'
    ])->name('geofences.all');

    Route::get('/geofences/types', [
        GeofenceController::class,
        'types'
    ])->name('geofences.types');

    Route::get('/geofence/device/{device}', [
        GeofenceController::class,
        'index'
    ])->name('geofences.index');

    Route::get('/vehicles/{device}/latest', [
        VehicleController::class,
        'latest'
    ])->name('vehicles.latest');

    Route::get('/vehicles/{device}/history', [
        VehicleController::class,
        'history'
    ])->name('vehicles.history');

    Route::get('/vehicles/{device}/playback', [
        VehicleController::class,
        'playback'
    ])->name('vehicles.playback');

    Route::get('/vehicles/{device}/summary', [
        VehicleController::class,
        'summary'
    ])->name('vehicles.summary');

    Route::get('/vehicles/{device}/activity', [
        VehicleController::class,
        'activity'
    ])->name('vehicles.activity');

    Route::get('/vehicles/{device}/stop', [
        VehicleController::class,
        'stop'
    ])->name('vehicles.stop');

    /**
     * Store Geofence
     */
    Route::post('/geofences', [
        GeofenceController::class,
        'store'
    ])->name('geofences.store');

    /**
     * Delete Multiple Geofence
     */
    Route::delete('/geofences', [
        GeofenceController::class,
        'destroyMany'
    ])->name('geofences.destroyMany');

    /**
     * Delete Single Geofence
     * Tetap dipertahankan agar kompatibel dengan fitur lama.
     */
    Route::delete('/geofences/{geofence}', [
        GeofenceController::class,
        'destroy'
    ])->name('geofences.destroy');

    /**
     * Update Geofence (nama, status, geometry)
     */
    Route::patch('/geofences/{geofence}', [
        GeofenceController::class,
        'update'
    ])->name('geofences.update');

    /**
     * Enable / Disable Geofence
     */
    Route::patch('/geofences/{geofence}/status', [
        GeofenceController::class,
        'updateStatus'
    ])->name('geofences.status');

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::patch('/profile', [
        ProfileController::class,
        'update'
    ])->name('profile.update');

    Route::delete('/profile', [
        ProfileController::class,
        'destroy'
    ])->name('profile.destroy');

    Route::get(

        '/api/location/search',

        [LocationController::class, 'search']

    );

    Route::get(

        '/api/location/reverse',

        [LocationController::class, 'reverse']

    );

    Route::get(
        '/api/search',
        SearchController::class
    )->name('search');

    Route::post(
        '/api/home-location',
        [LocationController::class, 'save']
    )->name('api.home-location.save');

    Route::delete(
        '/api/home-location',
        [LocationController::class, 'destroy']
    )->name('api.home-location.destroy');
});

require __DIR__ . '/auth.php';
