<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivateDeviceController;
use App\Http\Controllers\ActivateVehicleController;
use App\Http\Controllers\GeofenceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PublicTrackingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\Home\DeviceController as HomeDeviceController;
use App\Http\Controllers\Home\VehicleController as HomeVehicleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Vehicle\VehicleController;
use App\Http\Controllers\Vehicle\TripController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DeviceController as AdminDeviceController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Device Activation
|--------------------------------------------------------------------------
*/

Route::get('/devices/activate', [ActivateDeviceController::class, 'create'])
    ->name('devices.create');

Route::post('/devices/activate', [ActivateDeviceController::class, 'store'])
    ->name('devices.store')
    ->middleware('throttle:5,1');

/*
|--------------------------------------------------------------------------
| Public Tracking (Tanpa Login)
|--------------------------------------------------------------------------
|
| Diakses lewat link ber-signature yang dikirim ke Email/WhatsApp saat
| notification dibuat. Middleware "signed" menolak request yang
| signature-nya tidak valid atau sudah kedaluwarsa. Link mengarah ke
| notification tertentu (bukan device) supaya peta yang ditampilkan
| selalu konsisten dengan lokasi yang tertulis di pesan Email/WhatsApp.
|--------------------------------------------------------------------------
*/

Route::get('/track/{notification}', [PublicTrackingController::class, 'show'])
    ->name('track.show')
    ->middleware(['signed', 'throttle:30,1']);

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

Route::middleware(['auth', 'verified', 'is_user'])->group(function () {

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
    ])->name('home.devices.activate')
        ->middleware('throttle:5,1');

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

    Route::patch('/vehicles/{device}/speed-setting', [
        VehicleController::class,
        'updateSpeedSetting'
    ])->name('vehicles.speed-setting.update');

    Route::patch('/vehicles/{device}/notification-setting', [
        VehicleController::class,
        'updateNotificationSetting'
    ])->name('vehicles.notification-setting.update');

    Route::patch('/vehicles/{device}/geofence-setting', [
        VehicleController::class,
        'updateGeofenceSetting'
    ])->name('vehicles.geofence-setting.update');

    Route::get('/vehicles/{device}/geofence-history', [
        VehicleController::class,
        'geofenceHistory'
    ])->name('vehicles.geofence-history');

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

    Route::post('/vehicles/{device}/route', [
        VehicleController::class,
        'route'
    ])->name('vehicles.route');

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

    Route::get('/vehicles/{device}/trips', [
        TripController::class,
        'index'
    ])->name('vehicles.trips');

    /*
    |--------------------------------------------------------------------------
    | Export PDF (Riwayat Perjalanan / Riwayat Berhenti)
    |--------------------------------------------------------------------------
    | Rate-limit lebih ketat dari endpoint JSON biasa karena PDF generation
    | jauh lebih berat (render Blade + rasterisasi dompdf).
    |--------------------------------------------------------------------------
    */

    Route::get('/vehicles/{device}/export/travel', [
        VehicleController::class,
        'exportTravel'
    ])->name('vehicles.export.travel')
        ->middleware('throttle:10,1');

    Route::get('/vehicles/{device}/export/stop', [
        VehicleController::class,
        'exportStop'
    ])->name('vehicles.export.stop')
        ->middleware('throttle:10,1');

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

    )->middleware('throttle:30,1');

    Route::get(

        '/api/location/reverse',

        [LocationController::class, 'reverse']

    )->middleware('throttle:30,1');

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

    /*
    |--------------------------------------------------------------------------
    | Notification
    |--------------------------------------------------------------------------
    */

    Route::get('/notifications', [
        NotificationController::class,
        'index'
    ])->name('notifications.index');

    Route::patch('/notifications/mark-all-read', [
        NotificationController::class,
        'markAllAsRead'
    ])->name('notifications.mark-all-read');

    Route::patch('/notifications/{notification}/read', [
        NotificationController::class,
        'markAsRead'
    ])->name('notifications.read');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('devices/export-pdf/{key}', [AdminDeviceController::class, 'exportPdf'])->name('devices.export_pdf');
    Route::resource('devices', AdminDeviceController::class)->only(['index', 'store', 'destroy']);
    Route::resource('users', AdminUserController::class)->only(['index', 'destroy']);
    
    // Admin Profile
    Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__ . '/auth.php';
