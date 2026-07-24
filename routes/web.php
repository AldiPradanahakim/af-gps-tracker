<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivateDeviceController;
use App\Http\Controllers\ActivateVehicleController;
use App\Http\Controllers\GeofenceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Home\DeviceController as HomeDeviceController;
use App\Http\Controllers\Home\VehicleController as HomeVehicleController;

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
        ActivateVehicleController::class,
        'show'
    ])->name('vehicles.show');

    Route::get('/geofences', [
        GeofenceController::class,
        'all'
    ])->name('geofences.all');

    Route::get('/geofence/device/{device}', [
        GeofenceController::class,
        'index'
    ])->name('geofences.index');

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
});

require __DIR__ . '/auth.php';
