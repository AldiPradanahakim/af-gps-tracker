<div class="bg-white shadow-sm rounded-3xl p-4 mb-6">
    <div class="flex items-center justify-between gap-4">
        <button class="text-gray-700 p-2 rounded-full hover:bg-gray-100">
            ☰
        </button>

        <div class="flex-1 text-center">
            <div class="text-xs uppercase tracking-[0.3em] text-gray-500">AF GPS</div>
            <div class="mt-1 text-lg font-bold">{{ $vehicle?->vehicle_name ?? 'Pilih Kendaraan' }}</div>
            <div class="text-sm text-gray-500">{{ $vehicle?->vehicle_type ?? 'Motor / Mobil' }}</div>
        </div>

        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center uppercase font-bold">{{ substr(auth()->user()->name, 0, 1) }}</div>
        </div>
    </div>
</div>
