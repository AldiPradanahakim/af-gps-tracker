<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelHistory extends Model
{
    protected $fillable = [
        'device_log_id',
        'device_id',
        'location',
        'search_address',
        'received_at',
    ];

    protected $casts = [
        'location' => 'array',
        'received_at' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function deviceLog()
    {
        return $this->belongsTo(DeviceLog::class);
    }
}
