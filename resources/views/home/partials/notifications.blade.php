<div class="bg-white rounded-3xl shadow p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold">Notifikasi Terbaru</h2>
        <a href="#" class="text-sm text-blue-600 hover:underline">Lihat semua</a>
    </div>

    <div class="space-y-4">
        @forelse ($notifications as $notification)
            <div class="p-4 rounded-3xl bg-gray-50 border border-gray-100">
                <div class="font-semibold">{{ $notification->data['title'] ?? 'Notifikasi' }}</div>
                <div class="text-sm text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                <div class="text-sm text-gray-600 mt-2">{{ $notification->data['message'] ?? '-' }}</div>
            </div>
        @empty
            <div class="text-sm text-gray-500">Belum ada notifikasi.</div>
        @endforelse
    </div>
</div>
