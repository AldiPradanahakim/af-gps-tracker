<div
    id="activateDeviceModal"
    class="fixed inset-0 z-[99999] hidden items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm">

    <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-3xl bg-white shadow-2xl">

        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">

            <div>

                <h2 class="text-2xl font-bold text-slate-900">

                    Aktivasi Perangkat

                </h2>

                <p class="mt-1 text-sm text-slate-500">

                    Masukkan ID Perangkat dan Kata Sandi perangkat GPS.

                </p>

            </div>

            <button
                id="closeActivateDevice"
                type="button"
                class="rounded-xl p-2 hover:bg-slate-100">

                ✕

            </button>

        </div>

        <form
            id="activateDeviceForm"
            class="space-y-4 p-6">

            <div>

                <label class="mb-2 block text-sm font-semibold">

                    ID Perangkat

                </label>

                <input
                    id="deviceId"
                    name="device_id"
                    type="text"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3">

            </div>

            <div>

                <label class="mb-2 block text-sm font-semibold">

                    Kata Sandi Perangkat

                </label>

                <input
                    id="devicePassword"
                    name="device_password"
                    type="password"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3">

            </div>

            <div class="flex justify-end gap-3">

                <button
                    id="cancelActivateDevice"
                    type="button"
                    class="rounded-xl border border-slate-300 px-6 py-3">

                    Batal

                </button>

                <button
                    type="submit"
                    class="rounded-xl bg-[#2563EB] px-6 py-3 font-semibold text-white">

                    Aktivasi

                </button>

            </div>

        </form>

    </div>

</div>