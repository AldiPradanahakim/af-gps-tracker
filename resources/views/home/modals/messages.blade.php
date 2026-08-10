<div
    id="messagesModal"
    class="fixed inset-0 z-[99999] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm">

    <div class="flex max-h-[85vh] w-full max-w-xl flex-col rounded-3xl bg-white shadow-2xl">

        <div class="flex items-center justify-between border-b border-slate-200 px-8 py-6">

            <div>
                <h2 class="text-xl font-bold text-slate-900">Pesan</h2>
                <p class="mt-1 text-sm text-slate-500">Seluruh riwayat notifikasi, sudah maupun belum dibaca.</p>
            </div>

            <button
                id="messagesModalClose"
                type="button"
                class="flex h-9 w-9 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>

        <div id="messagesModalList" class="flex-1 divide-y divide-slate-100 overflow-y-auto">
            <div class="px-6 py-14 text-center text-slate-500">Memuat pesan...</div>
        </div>

        <div class="flex items-center justify-between border-t border-slate-200 px-6 py-4">

            <button
                id="messagesModalPrev"
                type="button"
                disabled
                class="rounded-[14px] border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40">
                <i class="fa-solid fa-chevron-left text-[11px]"></i>
                Sebelumnya
            </button>

            <span id="messagesModalPageInfo" class="text-xs text-slate-400"></span>

            <button
                id="messagesModalNext"
                type="button"
                disabled
                class="rounded-[14px] border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40">
                Selanjutnya
                <i class="fa-solid fa-chevron-right text-[11px]"></i>
            </button>

        </div>

    </div>

</div>
