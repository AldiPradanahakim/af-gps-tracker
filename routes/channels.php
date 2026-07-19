<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('vehicle.location', function ($user) {
    return true;
});

Broadcast::channel('vehicle.{deviceId}', function ($user, $deviceId) {
    return true;
});

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
