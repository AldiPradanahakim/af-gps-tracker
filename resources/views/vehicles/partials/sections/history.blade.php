<div class="space-y-6">

    <div class="rounded-[28px] border border-slate-200 bg-white vehicle-panel-shadow">
        <div class="border-b border-slate-100 px-6 py-5">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Riwayat Perjalanan</p>
            <h2 class="mt-2 text-xl font-semibold text-slate-900">Lihat Histori Perjalanan</h2>
            <p class="mt-1 text-sm text-slate-500">Filter perjalanan berdasarkan tanggal atau langsung mainkan kembali perjalanan hari ini.</p>
        </div>
        <div class="grid gap-4 p-6 lg:grid-cols-4">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Tanggal</label>
                <input id="historyDate" type="date" class="w-full rounded-[20px] border border-slate-300 px-4 py-3" />
            </div>
            <div class="flex items-end">
                <button id="historyLoadButton" type="button" class="w-full rounded-[20px] bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">Tampilkan</button>
            </div>
            <div class="flex items-end">
                <button id="historyTodayButton" type="button" class="w-full rounded-[20px] border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Hari Ini</button>
            </div>
            <div class="flex items-end">
                <button id="historyPlaybackButton" type="button" class="w-full rounded-[20px] bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Playback</button>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-[28px] border border-slate-200 bg-white vehicle-panel-shadow p-6">
            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Total Titik GPS</p>
            <p id="historyTotalPoint" class="mt-3 text-3xl font-semibold text-slate-900">0</p>
        </div>
        <div class="rounded-[28px] border border-slate-200 bg-white vehicle-panel-shadow p-6">
            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Total Jarak</p>
            <p id="historyTotalDistance" class="mt-3 text-3xl font-semibold text-slate-900">0 km</p>
        </div>
        <div class="rounded-[28px] border border-slate-200 bg-white vehicle-panel-shadow p-6">
            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Kecepatan Maksimum</p>
            <p id="historyMaxSpeed" class="mt-3 text-3xl font-semibold text-slate-900">0 km/jam</p>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-white vehicle-panel-shadow">
        <div class="border-b border-slate-100 px-6 py-5">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Timeline Perjalanan</p>
            <h2 class="mt-2 text-lg font-semibold text-slate-900">Detail Titik Perjalanan</h2>
        </div>
        <div id="historyTimeline" class="divide-y divide-slate-100 p-6">
            <div class="py-12 text-center text-slate-500">Belum ada data histori.</div>
        </div>
    </div>
</div>