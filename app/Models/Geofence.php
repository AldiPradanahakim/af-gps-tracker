<?php

namespace App\Models;

use App\Models\Device;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;

class Geofence extends Model
{
    protected $fillable = [
        'device_id',
        'name',
        'description',
        'type',
        'config',
        'status',
    ];

    protected $casts = [
        'config' => 'array',
        'status' => 'boolean',
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
