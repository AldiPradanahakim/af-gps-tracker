<?php

namespace App\Models;

use App\Models\Device;
use App\Models\TravelHistory;
use Illuminate\Database\Eloquent\Model;

class DeviceLog extends Model
{
    protected $fillable = [
        'device_id',
        'message_id',
        'payload',
        'status',
        'received_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'received_at' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function travelHistory()
    {
        return $this->hasOne(TravelHistory::class);
    }
}
