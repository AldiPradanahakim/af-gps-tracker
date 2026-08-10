<div class="space-y-6">

    <div class="rounded-[28px] border border-slate-200 bg-white vehicle-panel-shadow">

        <div class="border-b border-slate-100 px-6 py-5">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Notifikasi</p>
            <h2 class="mt-2 text-xl font-semibold text-slate-900">Pesan</h2>
            <p class="mt-1 text-sm text-slate-500">Seluruh riwayat notifikasi kendaraan Anda, sudah maupun belum dibaca.</p>
        </div>

        <div id="vehicleMessagesList" class="divide-y divide-slate-100">
            <div class="px-6 py-14 text-center text-slate-500">Memuat pesan...</div>
        </div>

        <div class="flex items-center justify-between border-t border-slate-100 px-6 py-4">

            <button
                id="vehicleMessagesPrev"
                type="button"
                disabled
                class="rounded-[14px] border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40">
                <i class="fa-solid fa-chevron-left text-[11px]"></i>
                Sebelumnya
            </button>

            <span id="vehicleMessagesPageInfo" class="text-xs text-slate-400"></span>

            <button
                id="vehicleMessagesNext"
                type="button"
                disabled
                class="rounded-[14px] border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40">
                Selanjutnya
                <i class="fa-solid fa-chevron-right text-[11px]"></i>
            </button>

        </div>

    </div>

</div>
