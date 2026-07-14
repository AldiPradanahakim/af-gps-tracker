<div class="bg-white rounded-3xl shadow p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <div class="text-xs uppercase tracking-[0.3em] text-gray-500">Device</div>
            <div class="text-xl font-bold mt-1">{{ $vehicle?->vehicle_name ?? 'Tidak ada kendaraan' }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ $vehicle?->plate_number ?? '-' }}</div>
        </div>
        <div class="text-right">
            <div class="text-xs text-gray-500">Jenis</div>
            <div class="text-sm font-semibold">{{ $vehicle?->vehicle_type ?? '-' }}</div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
        <div>
            <div class="text-xs text-gray-500">Status</div>
            <div class="text-base font-semibold {{ $device?->is_active ? 'text-green-600' : 'text-red-600' }}">
                {{ $device?->is_active ? 'Online' : 'Offline' }}
            </div>
        </div>

        <div>
            <div class="text-xs text-gray-500">Geofence</div>
            <div class="text-base font-semibold text-gray-700">{{ $activeGeofence?->name ?? 'Tidak ada' }}</div>
        </div>
    </div>
</div>
