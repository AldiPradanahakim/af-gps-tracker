<?php

use App\Models\Device;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Vehicle Channel
|--------------------------------------------------------------------------
*/

Broadcast::channel('vehicle.{deviceId}', function ($user, $deviceId) {

    return Device::where('id', $deviceId)
        ->where('user_id', $user->id)
        ->exists();
});

/*
|--------------------------------------------------------------------------
| User Channel
|--------------------------------------------------------------------------
*/

Broadcast::channel('user.{userId}', function ($user, $userId) {

    return hash_equals((string) $user->id, (string) $userId);
});
