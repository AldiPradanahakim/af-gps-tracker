<?php

namespace App\Models;

use App\Models\Device;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;

class StopHistory extends Model
{
    protected $fillable = [
        'device_id',
        'location',
        'search_address',
        'start_time',
        'end_time',
        'duration_seconds',
        'notification_sent',
    ];

    protected $casts = [
        'location' => 'array',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'notification_sent' => 'boolean',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
