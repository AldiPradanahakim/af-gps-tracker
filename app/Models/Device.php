<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasUuids;

    /**
     * Batas waktu sejak last_heartbeat terakhir supaya device masih
     * dianggap online. Selaras dengan jendela toleransi received_at
     * di GPSProcessingService (5 menit).
     */
    private const ONLINE_THRESHOLD_MINUTES = 5;

    protected $fillable = [

        'user_id',

        'device_id',

        'device_password',

        'mqtt_secret',

        'home_location',

        'stop_setting',

        'speed_setting',

        'notification_setting',

        'is_active',

        'last_heartbeat',

        'last_battery',

        'low_battery_active',

        'offline_notified_at',

        'overspeed_active',

        'is_inside_geofence',

        'activated_at',

    ];

    /**
     * device_password disimpan ter-hash dan tidak boleh ikut ter-serialize
     * ke JSON/array (mis. saat Device di-load lewat relasi Vehicle/Geofence).
     */
    protected $hidden = [

        'device_password',

        'mqtt_secret',

    ];

    protected $casts = [

        'device_password' => 'hashed',

        'mqtt_secret' => 'encrypted',

        'home_location' => 'array',

        'stop_setting' => 'array',

        'speed_setting' => 'array',

        'notification_setting' => 'array',

        'is_inside_geofence' => 'boolean',

        'overspeed_active' => 'boolean',

        'low_battery_active' => 'boolean',

        'activated_at' => 'datetime',

        'last_heartbeat' => 'datetime',

        'offline_notified_at' => 'datetime',

    ];

    /**
     * Online = device sudah pernah diaktivasi DAN heartbeat terakhirnya
     * masih dalam batas waktu ONLINE_THRESHOLD_MINUTES. is_active saja
     * tidak cukup - itu hanya menandakan device pernah diaktivasi, bukan
     * bahwa dia sedang mengirim data saat ini.
     */
    public function getIsOnlineAttribute(): bool
    {
        return (bool) $this->is_active
            && $this->last_heartbeat !== null
            && $this->last_heartbeat->greaterThan(
                now()->subMinutes(self::ONLINE_THRESHOLD_MINUTES)
            );
    }

    protected static function booted(): void
    {
        static::creating(function (Device $device) {
            if (empty($device->mqtt_secret)) {
                $device->mqtt_secret = bin2hex(random_bytes(32));
            }
        });
    }

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
