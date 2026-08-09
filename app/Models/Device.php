<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasUuids;

    protected $fillable = [

        'user_id',

        'device_id',

        'device_password',

        'home_location',

        'stop_setting',

        'notification_setting',

        'is_active',

        'last_heartbeat',

        'is_inside_geofence',

        'activated_at',

    ];

    /**
     * device_password disimpan ter-hash dan tidak boleh ikut ter-serialize
     * ke JSON/array (mis. saat Device di-load lewat relasi Vehicle/Geofence).
     */
    protected $hidden = [

        'device_password',

    ];

    protected $casts = [

        'device_password' => 'hashed',

        'home_location' => 'array',

        'stop_setting' => 'array',

        'notification_setting' => 'array',

        'is_inside_geofence' => 'boolean',

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
