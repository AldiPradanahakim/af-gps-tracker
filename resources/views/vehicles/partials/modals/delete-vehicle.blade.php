<div

    id="deleteVehicleModal"

    class="fixed inset-0 z-[99999] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm"

>

    <div
        class="w-full max-w-md overflow-hidden rounded-[24px] bg-white shadow-2xl"
    >

        {{-- ============================================== --}}
        {{-- Header --}}
        {{-- ============================================== --}}

        <div
            class="border-b border-slate-200 px-6 py-6 text-center"
        >

            <div
                class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100"
            >

                <i
                    class="fa-solid fa-trash text-2xl text-red-600"
                ></i>

            </div>

            <h2
                class="mt-5 text-[20px] font-bold text-slate-900"
            >
                Hapus Kendaraan
            </h2>

            <p
                class="mt-2 text-[13px] leading-6 text-slate-500"
            >
                Tindakan ini tidak dapat dibatalkan.
            </p>

        </div>

        {{-- ============================================== --}}
        {{-- Body --}}
        {{-- ============================================== --}}

        <div
            class="space-y-5 px-6 py-6"
        >

            <div
                class="rounded-xl border border-slate-200 bg-slate-50 p-4"
            >

                <p
                    class="text-[11px] uppercase tracking-[0.22em] text-slate-400"
                >
                    Kendaraan
                </p>

                <p

                    id="deleteVehicleName"

                    class="mt-2 text-[15px] font-semibold text-slate-900"

                >
                    -
                </p>

            </div>

            <div
                class="rounded-xl border border-red-100 bg-red-50 p-4"
            >

                <div
                    class="flex items-start gap-3"
                >

                    <i
                        class="fa-solid fa-triangle-exclamation mt-0.5 text-red-500"
                    ></i>

                    <p
                        class="text-[12px] leading-6 text-red-700"
                    >
                        Seluruh data kendaraan ini (riwayat perjalanan,
                        geofence, riwayat berhenti, notifikasi) akan
                        dihapus permanen dan tidak dapat dikembalikan.
                    </p>

                </div>

            </div>

        </div>

        {{-- ============================================== --}}
        {{-- Footer --}}
        {{-- ============================================== --}}

        <div
            class="flex justify-end gap-3 border-t border-slate-200 px-6 py-5"
        >

            <button

                id="cancelDeleteVehicle"

                type="button"

                class="rounded-xl border border-slate-300 px-5 py-2.5 text-[13px] font-semibold text-slate-700 transition hover:bg-slate-100"

            >
                Batal
            </button>

            <button

                id="confirmDeleteVehicle"

                type="button"

                class="rounded-xl bg-red-600 px-5 py-2.5 text-[13px] font-semibold text-white transition hover:bg-red-700"

            >

                <i class="fa-solid fa-trash mr-2"></i>

                Hapus Kendaraan

            </button>

        </div>

    </div>

</div>
