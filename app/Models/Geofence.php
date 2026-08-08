<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Geofence extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [

        'device_id',

        'name',

        'description',

        'type',

        'config',

        'status',

    ];

    /**
     * Attribute Casting
     */
    protected $casts = [

        'config' => 'array',

        'status' => 'boolean',

    ];

    /**
     * Device Relationship
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
