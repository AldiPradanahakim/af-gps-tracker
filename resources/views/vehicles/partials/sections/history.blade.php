<div class="space-y-6">

    <div class="rounded-[28px] border border-slate-200 bg-white vehicle-panel-shadow">
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Riwayat Perjalanan</p>
                <h2 class="mt-2 text-xl font-semibold text-slate-900">Lihat Histori Perjalanan</h2>
                <p class="mt-1 text-sm text-slate-500">Filter perjalanan berdasarkan rentang tanggal atau langsung mainkan kembali perjalanan hari ini.</p>
            </div>

            <button
                id="historyRefreshButton"
                type="button"
                class="inline-flex h-10 items-center gap-2 rounded-[16px] border border-slate-300 bg-white px-4 text-[13px] font-semibold text-slate-700 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-60"
            >
                <i class="fa-solid fa-rotate-right text-[12px]"></i>
                Refresh
            </button>
        </div>
        <div class="grid gap-4 p-6 lg:grid-cols-6">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Tanggal Mulai</label>
                <input id="historyStartDate" type="date" class="w-full rounded-[20px] border border-slate-300 px-4 py-3" />
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Tanggal Selesai</label>
                <input id="historyEndDate" type="date" class="w-full rounded-[20px] border border-slate-300 px-4 py-3" />
            </div>
            <div class="flex items-end">
                <button id="historyLoadButton" type="button" class="w-full rounded-[20px] bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">Tampilkan</button>
            </div>
            <div class="flex items-end">
                <button id="historyTodayButton" type="button" class="w-full rounded-[20px] border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Hari Ini</button>
            </div>
            <div class="flex items-end">
                <button id="historyPlaybackButton" type="button" class="w-full rounded-[20px] bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                    <i class="fa-solid fa-play mr-1"></i>
                    Playback
                </button>
            </div>
            <div class="flex items-end">
                <a id="historyExportPdfLink" href="{{ route('vehicles.export.travel', $device) }}" target="_blank" rel="noopener" class="w-full rounded-[20px] border border-slate-300 bg-white px-5 py-3 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    <i class="fa-solid fa-file-pdf mr-1"></i>
                    Export PDF
                </a>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- Rentang Playback (pilih titik awal & akhir yang dimainkan) --}}
        {{-- ========================================================= --}}

        <div id="historyPlaybackRange" class="hidden flex-wrap items-end gap-3 border-t border-slate-100 px-6 py-4">

            <div class="min-w-[220px] flex-1">
                <label class="mb-2 block text-xs font-medium text-slate-700">Dari Titik</label>
                <select id="historyPlaybackFrom" class="w-full rounded-[14px] border border-slate-300 px-3 py-2 text-sm text-slate-700"></select>
            </div>

            <div class="min-w-[220px] flex-1">
                <label class="mb-2 block text-xs font-medium text-slate-700">Sampai Titik</label>
                <select id="historyPlaybackTo" class="w-full rounded-[14px] border border-slate-300 px-3 py-2 text-sm text-slate-700"></select>
            </div>

            <button id="historyPlaybackRangeReset" type="button" class="rounded-[14px] border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                Reset Rentang
            </button>

        </div>

        {{-- ========================================================= --}}
        {{-- Kecepatan Playback --}}
        {{-- ========================================================= --}}

        <div class="flex flex-wrap items-center gap-3 border-t border-slate-100 px-6 py-4">

            <span class="text-[13px] font-medium text-slate-700">Kecepatan Playback</span>

            <div class="ml-auto grid grid-cols-4 gap-2">
                <button type="button" data-speed="2000" class="playback-speed rounded-[14px] border border-slate-300 px-4 py-2 text-sm font-semibold transition hover:bg-slate-100">0.5x</button>
                <button type="button" data-speed="1000" class="playback-speed rounded-[14px] bg-blue-600 px-4 py-2 text-sm font-semibold text-white">1x</button>
                <button type="button" data-speed="500" class="playback-speed rounded-[14px] border border-slate-300 px-4 py-2 text-sm font-semibold transition hover:bg-slate-100">2x</button>
                <button type="button" data-speed="250" class="playback-speed rounded-[14px] border border-slate-300 px-4 py-2 text-sm font-semibold transition hover:bg-slate-100">4x</button>
            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- Kontrol Playback (muncul saat playback berjalan/dijeda) --}}
        {{-- ========================================================= --}}

        <div id="historyPlaybackControls" class="hidden flex-wrap items-center gap-3 border-t border-slate-100 px-6 py-4">

            <button id="historyPlaybackPauseButton" type="button" class="hidden rounded-[16px] border border-amber-300 bg-amber-50 px-4 py-2.5 text-sm font-semibold text-amber-700 transition hover:bg-amber-100">
                <i class="fa-solid fa-pause mr-1.5"></i>
                Jeda
            </button>

            <button id="historyPlaybackResumeButton" type="button" class="hidden rounded-[16px] border border-emerald-300 bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100">
                <i class="fa-solid fa-play mr-1.5"></i>
                Lanjutkan
            </button>

            <button id="historyPlaybackStopButton" type="button" class="rounded-[16px] border border-red-300 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-100">
                <i class="fa-solid fa-stop mr-1.5"></i>
                Berhenti
            </button>

            <span id="historyPlaybackProgress" class="ml-auto text-[13px] font-medium text-slate-500">
                Titik 0 dari 0
            </span>

        </div>
    </div>

    <div id="historyEmptyState" class="hidden rounded-[28px] border border-slate-200 bg-white vehicle-panel-shadow px-6 py-14 text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
            <i class="fa-solid fa-route text-2xl text-slate-400"></i>
        </div>
        <h4 class="mt-5 text-[15px] font-semibold text-slate-900">Tidak ada riwayat perjalanan</h4>
        <p class="mt-2 text-[13px] text-slate-500">Tidak ada data GPS pada rentang tanggal yang dipilih.</p>
    </div>

    <div id="historyContent">

        <div class="grid gap-5 lg:grid-cols-4">
            <div class="rounded-[24px] border border-slate-200 bg-white vehicle-panel-shadow p-5">
                <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Total Titik GPS</p>
                <p id="historyTotalPoint" class="mt-3 text-2xl font-semibold text-slate-900">0</p>
            </div>
            <div class="rounded-[24px] border border-slate-200 bg-white vehicle-panel-shadow p-5">
                <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Total Jarak</p>
                <p id="historyTotalDistance" class="mt-3 text-2xl font-semibold text-slate-900">0 km</p>
            </div>
            <div class="rounded-[24px] border border-slate-200 bg-white vehicle-panel-shadow p-5">
                <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Total Durasi</p>
                <p id="historyTotalDuration" class="mt-3 text-2xl font-semibold text-slate-900">-</p>
            </div>
            <div class="rounded-[24px] border border-slate-200 bg-white vehicle-panel-shadow p-5">
                <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Kecepatan Maksimum</p>
                <p id="historyMaxSpeed" class="mt-3 text-2xl font-semibold text-slate-900">0 km/jam</p>
            </div>
            <div class="rounded-[24px] border border-slate-200 bg-white vehicle-panel-shadow p-5">
                <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Kecepatan Rata-rata</p>
                <p id="historyAverageSpeed" class="mt-3 text-2xl font-semibold text-slate-900">0 km/jam</p>
            </div>
            <div class="rounded-[24px] border border-slate-200 bg-white vehicle-panel-shadow p-5">
                <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Waktu Awal</p>
                <p id="historyFirstTime" class="mt-3 text-lg font-semibold text-slate-900">-</p>
            </div>
            <div class="rounded-[24px] border border-slate-200 bg-white vehicle-panel-shadow p-5">
                <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Waktu Akhir</p>
                <p id="historyLastTime" class="mt-3 text-lg font-semibold text-slate-900">-</p>
            </div>
        </div>

        <div class="mt-6 rounded-[28px] border border-slate-200 bg-white vehicle-panel-shadow">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 px-6 py-5">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Timeline Perjalanan</p>
                    <h2 class="mt-2 text-lg font-semibold text-slate-900">Detail Titik Perjalanan</h2>
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-slate-500">Tampilan:</span>
                        <button id="historyViewPointsButton" type="button" class="rounded-[12px] bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white">Titik GPS</button>
                        <button id="historyViewTripsButton" type="button" class="rounded-[12px] border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100">Perjalanan</button>
                    </div>
                    <div id="historySortButtons" class="flex items-center gap-2">
                        <span class="text-xs font-medium text-slate-500">Urutkan:</span>
                        <button id="historySortDesc" type="button" class="rounded-[12px] bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white">Terbaru</button>
                        <button id="historySortAsc" type="button" class="rounded-[12px] border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100">Terlama</button>
                    </div>
                </div>
            </div>
            <div id="historyTimeline" class="divide-y divide-slate-100 p-6">
                <div class="py-12 text-center text-slate-500">Belum ada data histori.</div>
            </div>
            <div id="historyTripList" class="hidden grid gap-4 p-6 sm:grid-cols-2">
                <div class="col-span-full py-12 text-center text-slate-500">Belum ada data perjalanan.</div>
            </div>
        </div>

    </div>

</div>