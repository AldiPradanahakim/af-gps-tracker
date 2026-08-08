<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DeviceLog extends Model
{
    use HasUuids;

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

    public function travelHistories()
    {
        return $this->hasMany(TravelHistory::class);
    }
}
