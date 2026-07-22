<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Vehicle Channel
|--------------------------------------------------------------------------
*/

Broadcast::channel('vehicle.{deviceId}', function ($user, $deviceId) {

    return true;
});

/*
|--------------------------------------------------------------------------
| User Channel
|--------------------------------------------------------------------------
*/

Broadcast::channel('user.{userId}', function ($user, $userId) {

    return (int) $user->id === (int) $userId;
});
