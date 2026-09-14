<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Riwayat masuk/keluar geofence.
 *
 * Dicatat terpisah dari tabel notifications karena notifikasi bisa
 * dihapus atau ditandai dibaca oleh pengguna, sedangkan riwayat harus
 * permanen - dan riwayat tetap tercatat walaupun seluruh kanal
 * notifikasi dimatikan.
 */
class GeofenceHistory extends Model
{
    use HasUuids;

    protected $fillable = [

        'device_id',

        'geofence_id',

        'geofence_name',

        'geofence_type',

        'event',

        'location',

        'search_address',

        'occurred_at',

        'duration_seconds',

    ];

    protected $casts = [

        'location' => 'array',

        'occurred_at' => 'datetime',

        'duration_seconds' => 'integer',

    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function geofence(): BelongsTo
    {
        return $this->belongsTo(Geofence::class);
    }

    /**
     * Label tipe geofence untuk tampilan.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->geofence_type) {
            'radius' => 'Radius',
            'administrative' => 'Administratif',
            'custom' => 'Poligon',
            default => (string) $this->geofence_type,
        };
    }
}
