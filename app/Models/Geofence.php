<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

        'is_inside',

        'last_exit_notified_at',

        'state_changed_at',

    ];

    /**
     * Attribute Casting
     */
    protected $casts = [

        'config' => 'array',

        'status' => 'boolean',

        'is_inside' => 'boolean',

        'last_exit_notified_at' => 'datetime',

        'state_changed_at' => 'datetime',

    ];

    /**
     * Device Relationship
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Riwayat Masuk/Keluar
     */
    public function histories(): HasMany
    {
        return $this->hasMany(GeofenceHistory::class);
    }

    /**
     * Label tipe geofence untuk tampilan dan pesan notifikasi.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'radius' => 'Radius',
            'administrative' => 'Administratif',
            'custom' => 'Poligon',
            default => (string) $this->type,
        };
    }
}
