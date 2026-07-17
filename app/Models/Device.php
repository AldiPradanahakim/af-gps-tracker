<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [

        'user_id',

        'device_id',

        'device_password',

        'home_location',

        'stop_setting',

        'notification_setting',

        'is_active',

        'last_heartbeat',

        'activated_at',

    ];

    protected $casts = [

        'home_location' => 'array',

        'stop_setting' => 'array',

        'notification_setting' => 'array',

        'activated_at' => 'datetime',

        'last_heartbeat' => 'datetime',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class);
    }

    public function deviceLogs()
    {
        return $this->hasMany(DeviceLog::class);
    }

    public function geofences()
    {
        return $this->hasMany(Geofence::class);
    }

    public function travelHistories()
    {
        return $this->hasMany(TravelHistory::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function stopHistories()
    {
        return $this->hasMany(StopHistory::class);
    }
}
