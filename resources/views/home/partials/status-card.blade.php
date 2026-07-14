<div class="bg-white rounded-3xl shadow p-6 mb-6">
    <div class="grid grid-cols-2 gap-4">
        <div class="border-r border-gray-100 pr-4">
            <div class="text-xs uppercase tracking-[0.3em] text-gray-500">Heartbeat</div>
            <div class="mt-2 text-lg font-semibold text-gray-900">{{ $device?->last_heartbeat?->diffForHumans() ?? 'Belum ada' }}</div>
        </div>
        <div>
            <div class="text-xs uppercase tracking-[0.3em] text-gray-500">Geofence Aktif</div>
            <div class="mt-2 text-lg font-semibold text-gray-900">{{ $activeGeofence?->name ?? 'Tidak ada' }}</div>
        </div>
    </div>
</div>
