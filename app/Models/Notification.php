<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasUuids;

    protected $fillable = [
        'device_id',
        'geofence_id',
        'stop_history_id',
        'type',
        'data',
        'status',
        'sent_at',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function geofence()
    {
        return $this->belongsTo(Geofence::class);
    }

    public function stopHistory()
    {
        return $this->belongsTo(StopHistory::class);
    }
}
