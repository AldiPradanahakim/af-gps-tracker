<div class="space-y-6">

    <div class="rounded-[28px] border border-slate-200 bg-white vehicle-panel-shadow">
        <div class="border-b border-slate-100 px-6 py-5">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Pengaturan Kendaraan</p>
            <h2 class="mt-2 text-xl font-semibold text-slate-900">Pengaturan Monitoring</h2>
            <p class="mt-1 text-sm text-slate-500">Sesuaikan preferensi tampilan dan perilaku kendaraan di peta.</p>
        </div>
        <div class="space-y-5 p-6">
            <label class="flex items-center justify-between rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-4">
                <span class="font-medium text-slate-700">Follow Marker</span>
                <input id="settingFollowMarker" type="checkbox" class="h-5 w-5" checked>
            </label>
            <label class="flex items-center justify-between rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-4">
                <span class="font-medium text-slate-700">Auto Center Map</span>
                <input id="settingAutoCenter" type="checkbox" class="h-5 w-5" checked>
            </label>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white vehicle-panel-shadow">
        <div class="border-b border-slate-100 px-6 py-5">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Playback</p>
            <h2 class="mt-2 text-xl font-semibold text-slate-900">Kecepatan Playback</h2>
            <p class="mt-1 text-sm text-slate-500">Atur cepat atau lambatnya animasi kembali perjalanan.</p>
        </div>
        <div class="space-y-6 p-6">
            <p class="text-sm font-medium text-slate-700">Playback Speed</p>
            <div class="grid grid-cols-4 gap-3">
                <button type="button" data-speed="2000" class="playback-speed rounded-[20px] border border-slate-300 px-4 py-3 text-sm font-semibold transition hover:bg-slate-100">0.5x</button>
                <button type="button" data-speed="1000" class="playback-speed rounded-[20px] bg-blue-600 px-4 py-3 text-sm font-semibold text-white">1x</button>
                <button type="button" data-speed="500" class="playback-speed rounded-[20px] border border-slate-300 px-4 py-3 text-sm font-semibold transition hover:bg-slate-100">2x</button>
                <button type="button" data-speed="250" class="playback-speed rounded-[20px] border border-slate-300 px-4 py-3 text-sm font-semibold transition hover:bg-slate-100">4x</button>
            </div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white vehicle-panel-shadow">
        <div class="border-b border-slate-100 px-6 py-5">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Notifikasi</p>
            <h2 class="mt-2 text-xl font-semibold text-slate-900">Pengaturan Notifikasi</h2>
            <p class="mt-1 text-sm text-slate-500">Pilih pemberitahuan yang ingin Anda terima.</p>
        </div>
        <div class="space-y-4 p-6">
            <label class="flex items-center justify-between rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-4">
                <span>Overspeed</span>
                <input id="settingOverspeed" type="checkbox" class="h-5 w-5">
            </label>
            <label class="flex items-center justify-between rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-4">
                <span>Stop Detection</span>
                <input id="settingStop" type="checkbox" class="h-5 w-5">
            </label>
            <label class="flex items-center justify-between rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-4">
                <span>Aktifkan Notifikasi</span>
                <input id="settingNotifications" type="checkbox" class="h-5 w-5">
            </label>
        </div>
    </div>
</div>

