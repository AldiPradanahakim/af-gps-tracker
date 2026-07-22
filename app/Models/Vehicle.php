<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'device_id',
        'vehicle_name',
        'vehicle_type',
        'plate_number',
        'marker_icon',
        'marker_color',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
