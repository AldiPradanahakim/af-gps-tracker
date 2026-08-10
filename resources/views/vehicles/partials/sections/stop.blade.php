{{-- ========================================================= --}}
{{-- STOP DETECTION --}}
{{-- ========================================================= --}}

<section
    class="overflow-hidden rounded-[26px] border border-slate-200 bg-white vehicle-panel-shadow pt-4" 
>

    {{-- ===================================================== --}}
    {{-- Header --}}
    {{-- ===================================================== --}}

    <div
        class="flex flex-col gap-4 border-b border-slate-200 px-6 py-5 lg:flex-row lg:items-center lg:justify-between"
    >

        <div>

            <p
                class="text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-400"
            >
                STOP DETECTION
            </p>

            <h2
                class="mt-2 text-[20px] font-bold text-slate-900"
            >
                Pengaturan Stop Detection
            </h2>

            <p
                class="mt-2 max-w-3xl text-[13px] leading-6 text-slate-500"
            >
                Atur kapan kendaraan dianggap berhenti berdasarkan durasi
                kendaraan berada pada kecepatan 0 km/jam serta tentukan media
                notifikasi yang akan digunakan ketika Stop Detection terjadi.
            </p>

        </div>

        <button

            id="editStopDetectionButton"

            type="button"

            class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 text-[12px] font-semibold text-slate-700 transition hover:border-blue-300 hover:text-blue-600"

        >

            <i
                class="fa-solid fa-pen-to-square"
            ></i>

            Edit Pengaturan

        </button>

    </div>

    {{-- ===================================================== --}}
    {{-- READ MODE --}}
    {{-- ===================================================== --}}

    <div

        id="stopDetectionReadContainer"

    >

        <div
            class="px-6 py-6"
        >

            <div
                class="grid grid-cols-[220px_20px_1fr] gap-y-5 text-[13px]"
            >

                {{-- ===================================================== --}}
                {{-- Status Feature --}}
                {{-- ===================================================== --}}

                <div
                    class="text-slate-500"
                >
                    Status Stop Detection
                </div>

                <div
                    class="text-center text-slate-400"
                >
                    :
                </div>

                <div>

                    @if($stopDetection?->enabled)

                        <span
                            class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-[12px] font-semibold text-emerald-600"
                        >

                            <span
                                class="h-2 w-2 rounded-full bg-emerald-500"
                            ></span>

                            Aktif

                        </span>

                    @else

                        <span
                            class="inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1 text-[12px] font-semibold text-red-600"
                        >

                            <span
                                class="h-2 w-2 rounded-full bg-red-500"
                            ></span>

                            Nonaktif

                        </span>

                    @endif

                </div>

                {{-- ===================================================== --}}
                {{-- Stop Timer --}}
                {{-- ===================================================== --}}

                <div
                    class="text-slate-500"
                >
                    Durasi Kendaraan Berhenti
                </div>

                <div
                    class="text-center text-slate-400"
                >
                    :
                </div>

                <div
                    class="font-semibold text-slate-900"
                >
                    {{ $stopDetection?->stop_minutes ?? '-' }}
                    Menit
                </div>

                {{-- ===================================================== --}}
                {{-- System Notification --}}
                {{-- ===================================================== --}}

                <div
                    class="text-slate-500"
                >
                    Notifikasi Sistem
                </div>

                <div
                    class="text-center text-slate-400"
                >
                    :
                </div>

                <div>

                    @if($stopDetection?->system_notification)

                        <span
                            class="font-semibold text-emerald-600"
                        >
                            Aktif
                        </span>

                    @else

                        <span
                            class="font-semibold text-slate-500"
                        >
                            Nonaktif
                        </span>

                    @endif

                </div>

                {{-- ===================================================== --}}
                {{-- Email --}}
                {{-- ===================================================== --}}

                <div
                    class="text-slate-500"
                >
                    Notifikasi Email
                </div>

                <div
                    class="text-center text-slate-400"
                >
                    :
                </div>

                <div>

                    @if($stopDetection?->email_notification)

                        <span
                            class="font-semibold text-emerald-600"
                        >
                            Aktif
                        </span>

                    @else

                        <span
                            class="font-semibold text-slate-500"
                        >
                            Nonaktif
                        </span>

                    @endif

                </div>

                {{-- ===================================================== --}}
                {{-- WhatsApp --}}
                {{-- ===================================================== --}}

                <div
                    class="text-slate-500"
                >
                    Notifikasi WhatsApp
                </div>

                <div
                    class="text-center text-slate-400"
                >
                    :
                </div>

                <div>

                    @if($stopDetection?->whatsapp_notification)

                        <span
                            class="font-semibold text-emerald-600"
                        >
                            Aktif
                        </span>

                    @else

                        <span
                            class="font-semibold text-slate-500"
                        >
                            Nonaktif
                        </span>

                    @endif

                </div>

            </div>

        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- STATUS CARD --}}
    {{-- ===================================================== --}}

    <div

        id="stopDetectionStatusCard"

    >

        <div
            class="border-t border-slate-200 bg-slate-50 px-6 py-5"
        >

            <div
                class="grid grid-cols-2 gap-4 xl:grid-cols-4"
            >

                {{-- ===================================================== --}}
                {{-- Feature --}}
                {{-- ===================================================== --}}

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-4"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <p
                                class="text-[11px] font-medium text-slate-500"
                            >
                                Stop Detection
                            </p>

                            <h4
                                class="mt-2 text-[18px] font-bold text-slate-900"
                            >
                                {{ $stopDetection?->enabled ? 'ON' : 'OFF' }}
                            </h4>

                        </div>

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50"
                        >

                            <i
                                class="fa-solid fa-car-side text-blue-600"
                            ></i>

                        </div>

                    </div>

                </div>

                {{-- ===================================================== --}}
                {{-- Stop Timer --}}
                {{-- ===================================================== --}}

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-4"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <p
                                class="text-[11px] font-medium text-slate-500"
                            >
                                Stop Timer
                            </p>

                            <h4
                                class="mt-2 text-[18px] font-bold text-slate-900"
                            >
                                {{ $stopDetection?->stop_minutes ?? '-' }}

                                <span
                                    class="text-[12px] font-medium text-slate-500"
                                >
                                    Menit
                                </span>

                            </h4>

                        </div>

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50"
                        >

                            <i
                                class="fa-solid fa-clock text-amber-600"
                            ></i>

                        </div>

                    </div>

                </div>

                {{-- ===================================================== --}}
                {{-- Email --}}
                {{-- ===================================================== --}}

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-4"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <p
                                class="text-[11px] font-medium text-slate-500"
                            >
                                Email
                            </p>

                            <h4
                                class="mt-2 text-[18px] font-bold text-slate-900"
                            >
                                {{ $stopDetection?->email_notification ? 'ON' : 'OFF' }}
                            </h4>

                        </div>

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50"
                        >

                            <i
                                class="fa-solid fa-envelope text-emerald-600"
                            ></i>

                        </div>

                    </div>

                </div>

                {{-- ===================================================== --}}
                {{-- WhatsApp --}}
                {{-- ===================================================== --}}

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-4"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <p
                                class="text-[11px] font-medium text-slate-500"
                            >
                                WhatsApp
                            </p>

                            <h4
                                class="mt-2 text-[18px] font-bold text-slate-900"
                            >
                                {{ $stopDetection?->whatsapp_notification ? 'ON' : 'OFF' }}
                            </h4>

                        </div>

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-50"
                        >

                            <i
                                class="fa-brands fa-whatsapp text-green-600"
                            ></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- ===================================================== --}}
    {{-- EDIT MODE --}}
    {{-- ===================================================== --}}

    <div

        id="stopDetectionEditContainer"

        class="hidden p-6"

    >

            <form

                id="stopDetectionForm"

                class="space-y-6"

            >

                {{-- ===================================================== --}}
                {{-- Status Feature --}}
                {{-- ===================================================== --}}

                <div
                    class="rounded-2xl border border-slate-200 bg-slate-50 p-5"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <h3
                                class="text-[14px] font-semibold text-slate-900"
                            >
                                Stop Detection
                            </h3>

                            <p
                                class="mt-1 text-[12px] leading-5 text-slate-500"
                            >
                                Aktifkan atau nonaktifkan fitur pendeteksi kendaraan berhenti.
                            </p>

                        </div>

                        <label class="relative inline-flex cursor-pointer items-center">

                            <input

                                id="stopDetectionEnabled"

                                name="enabled"

                                type="checkbox"

                                class="peer sr-only"

                                @checked($stopDetection?->enabled)

                            >

                            <div
                                class="h-7 w-12 rounded-full bg-slate-300 transition peer-checked:bg-blue-600 after:absolute after:left-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all peer-checked:after:translate-x-5"
                            ></div>

                        </label>

                    </div>

                </div>

                {{-- ===================================================== --}}
                {{-- Stop Timer --}}
                {{-- ===================================================== --}}

                <div>

                    <label
                        class="mb-2 block text-[12px] font-semibold text-slate-700"
                    >
                        Durasi Kendaraan Berhenti
                    </label>

                    <div
                        class="flex items-center gap-3"
                    >

                        <select

                            id="stopMinutes"

                            name="stop_minutes"

                            class="h-11 w-40 rounded-xl border border-slate-300 px-4 text-center text-[14px] font-semibold outline-none transition focus:border-blue-500"

                        >

                            @foreach([1, 5, 10, 15] as $minuteOption)

                                <option
                                    value="{{ $minuteOption }}"
                                    @selected(($stopDetection?->stop_minutes ?? 5) == $minuteOption)
                                >
                                    {{ $minuteOption }} Menit
                                </option>

                            @endforeach

                        </select>

                    </div>

                    <p
                        class="mt-2 text-[12px] text-slate-400"
                    >
                        Kendaraan dianggap berhenti apabila berada pada kecepatan
                        <strong>0 km/jam</strong> selama durasi di atas.
                    </p>

                </div>

                {{-- ===================================================== --}}
                {{-- Notification --}}
                {{-- ===================================================== --}}

                <div
                    class="space-y-4"
                >

                    {{-- ============================== --}}
                    {{-- System --}}
                    {{-- ============================== --}}

                    <div
                        class="flex items-center justify-between rounded-2xl border border-slate-200 p-4"
                    >

                        <div>

                            <h4
                                class="text-[13px] font-semibold text-slate-900"
                            >
                                Notifikasi Sistem
                            </h4>

                            <p
                                class="mt-1 text-[12px] text-slate-500"
                            >
                                Menampilkan notifikasi pada dashboard aplikasi.
                                Selalu aktif dan tidak dapat dinonaktifkan.
                            </p>

                        </div>

                        <span
                            class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-[12px] font-semibold text-emerald-600"
                        >
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Selalu Aktif
                        </span>

                    </div>

                    {{-- ============================== --}}
                    {{-- Email --}}
                    {{-- ============================== --}}

                    <div
                        class="flex items-center justify-between rounded-2xl border border-slate-200 p-4"
                    >

                        <div>

                            <h4
                                class="text-[13px] font-semibold text-slate-900"
                            >
                                Notifikasi Email
                            </h4>

                            <p
                                class="mt-1 text-[12px] text-slate-500"
                            >
                                Mengirim email ketika Stop Detection terjadi.
                            </p>

                        </div>

                        <label class="relative inline-flex cursor-pointer items-center">

                            <input

                                id="emailNotification"

                                name="email_notification"

                                type="checkbox"

                                class="peer sr-only"

                                @checked($stopDetection?->email_notification)

                            >

                            <div
                                class="h-7 w-12 rounded-full bg-slate-300 transition peer-checked:bg-blue-600 after:absolute after:left-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all peer-checked:after:translate-x-5"
                            ></div>

                        </label>

                    </div>

                    {{-- ============================== --}}
                    {{-- WhatsApp --}}
                    {{-- ============================== --}}

                    <div
                        class="flex items-center justify-between rounded-2xl border border-slate-200 p-4"
                    >

                        <div>

                            <h4
                                class="text-[13px] font-semibold text-slate-900"
                            >
                                Notifikasi WhatsApp
                            </h4>

                            <p
                                class="mt-1 text-[12px] text-slate-500"
                            >
                                Mengirim WhatsApp ketika Stop Detection terjadi.
                            </p>

                        </div>

                        <label class="relative inline-flex cursor-pointer items-center">

                            <input

                                id="whatsappNotification"

                                name="whatsapp_notification"

                                type="checkbox"

                                class="peer sr-only"

                                @checked($stopDetection?->whatsapp_notification)

                            >

                            <div
                                class="h-7 w-12 rounded-full bg-slate-300 transition peer-checked:bg-blue-600 after:absolute after:left-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all peer-checked:after:translate-x-5"
                            ></div>

                        </label>

                    </div>

                </div>

                {{-- ===================================================== --}}
                {{-- Action --}}
                {{-- ===================================================== --}}

                <div
                    class="flex justify-end gap-3 border-t border-slate-200 pt-5"
                >

                    <button

                        id="cancelStopDetectionEdit"

                        type="button"

                        class="rounded-xl border border-slate-300 px-5 py-2.5 text-[13px] font-semibold text-slate-700 transition hover:bg-slate-100"

                    >

                        Batal

                    </button>

                    <button

                        id="saveStopDetection"

                        type="submit"

                        class="rounded-xl bg-blue-600 px-5 py-2.5 text-[13px] font-semibold text-white transition hover:bg-blue-700"

                    >

                        <i class="fa-solid fa-floppy-disk mr-2"></i>

                        Simpan Pengaturan

                    </button>

                </div>

            </form>

    </div>

</section>

{{-- ========================================================= --}}
{{-- SUMMARY --}}
{{-- ========================================================= --}}

<section

    id="stopDetectionSummarySection"

>

    {{-- ========================================================= --}}
    {{-- STOP DETECTION SUMMARY --}}
    {{-- ========================================================= --}}

    <section
        id="stopDetectionSummarySection"
        class="overflow-hidden rounded-[26px] border border-slate-200 bg-white vehicle-panel-shadow mt-5"
    >

        {{-- ===================================================== --}}
        {{-- Header --}}
        {{-- ===================================================== --}}

        <div
            class="border-b border-slate-200 px-6 py-5"
        >

            <p
                class="text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-400"
            >
                STOP SUMMARY
            </p>

            <h2
                class="mt-2 text-[20px] font-bold text-slate-900"
            >
                Ringkasan Stop Detection
            </h2>

            <p
                class="mt-2 text-[13px] leading-6 text-slate-500"
            >
                Statistik kendaraan berhenti berdasarkan hasil Stop Detection.
            </p>

        </div>

        {{-- ===================================================== --}}
        {{-- Summary Card --}}
        {{-- ===================================================== --}}

        <div
            class="grid gap-5 p-6 md:grid-cols-2 xl:grid-cols-4"
        >

            {{-- =============================================== --}}
            {{-- Total Stop --}}
            {{-- =============================================== --}}

            <div
                class="rounded-2xl border border-slate-200 bg-slate-50 p-5"
            >

                <div
                    class="flex items-center justify-between"
                >

                    <p
                        class="text-[12px] font-medium text-slate-500"
                    >
                        Total Stop
                    </p>

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100"
                    >

                        <i
                            class="fa-solid fa-stop text-blue-600"
                        ></i>

                    </div>

                </div>

                <h3
                    class="mt-5 text-[28px] font-bold text-slate-900"
                >
                    {{ $stopSummary['total_stop'] ?? 0 }}
                </h3>

            </div>

            {{-- =============================================== --}}
            {{-- Hari Ini --}}
            {{-- =============================================== --}}

            <div
                class="rounded-2xl border border-slate-200 bg-slate-50 p-5"
            >

                <div
                    class="flex items-center justify-between"
                >

                    <p
                        class="text-[12px] font-medium text-slate-500"
                    >
                        Hari Ini
                    </p>

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100"
                    >

                        <i
                            class="fa-solid fa-calendar-day text-emerald-600"
                        ></i>

                    </div>

                </div>

                <h3
                    class="mt-5 text-[28px] font-bold text-slate-900"
                >
                    {{ $stopSummary['today_stop'] ?? 0 }}
                </h3>

            </div>

            {{-- =============================================== --}}
            {{-- Durasi Terlama --}}
            {{-- =============================================== --}}

            <div
                class="rounded-2xl border border-slate-200 bg-slate-50 p-5"
            >

                <div
                    class="flex items-center justify-between"
                >

                    <p
                        class="text-[12px] font-medium text-slate-500"
                    >
                        Durasi Terlama
                    </p>

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100"
                    >

                        <i
                            class="fa-solid fa-clock text-amber-600"
                        ></i>

                    </div>

                </div>

                <h3
                    class="mt-5 text-[28px] font-bold text-slate-900"
                >
                    {{ $stopSummary['longest_stop'] ?? '-' }}
                </h3>

            </div>

            {{-- =============================================== --}}
            {{-- Total Waktu Stop --}}
            {{-- =============================================== --}}

            <div
                class="rounded-2xl border border-slate-200 bg-slate-50 p-5"
            >

                <div
                    class="flex items-center justify-between"
                >

                    <p
                        class="text-[12px] font-medium text-slate-500"
                    >
                        Total Waktu Stop
                    </p>

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-100"
                    >

                        <i
                            class="fa-solid fa-hourglass-half text-red-600"
                        ></i>

                    </div>

                </div>

                <h3
                    class="mt-5 text-[28px] font-bold text-slate-900"
                >
                    {{ $stopSummary['total_duration'] ?? '-' }}
                </h3>

            </div>

        </div>

    </section>

</section>

{{-- ========================================================= --}}
{{-- HISTORY --}}
{{-- ========================================================= --}}

<section

    id="stopDetectionHistorySection"

>

    {{-- ========================================================= --}}
    {{-- STOP HISTORY --}}
    {{-- ========================================================= --}}

    <section
        id="stopDetectionHistorySection"
        class="overflow-hidden rounded-[26px] border border-slate-200 bg-white vehicle-panel-shadow mt-5"
    >

        {{-- ===================================================== --}}
        {{-- Header --}}
        {{-- ===================================================== --}}

        <div
            class="flex items-center justify-between border-b border-slate-200 px-6 py-5"
        >

            <div>

                <p
                    class="text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-400"
                >
                    STOP HISTORY
                </p>

                <h2
                    class="mt-2 text-[20px] font-bold text-slate-900"
                >
                    Riwayat Kendaraan Berhenti
                </h2>

                <p
                    class="mt-2 text-[13px] leading-6 text-slate-500"
                >
                    Seluruh riwayat kendaraan berhenti yang berhasil dideteksi
                    berdasarkan pengaturan Stop Detection.
                </p>

            </div>

            <div class="flex items-center gap-2">

                <a

                    id="stopHistoryExportPdfLink"

                    href="{{ route('vehicles.export.stop', $device) }}"

                    target="_blank"

                    rel="noopener"

                    class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-[12px] font-semibold text-slate-700 transition hover:bg-slate-100"

                >

                    <i class="fa-solid fa-file-pdf"></i>

                    Export PDF

                </a>

                <button

                    id="refreshStopHistory"

                    type="button"

                    class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-[12px] font-semibold text-slate-700 transition hover:bg-slate-100"

                >

                    <i class="fa-solid fa-rotate"></i>

                    Refresh

                </button>

            </div>

        </div>

        {{-- ===================================================== --}}
        {{-- Table --}}
        {{-- ===================================================== --}}

        <div
            class="overflow-x-auto"
        >

            <table
                class="min-w-full"
            >

                <thead
                    class="bg-slate-50"
                >

                    <tr>

                        <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            Mulai
                        </th>

                        <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            Selesai
                        </th>

                        <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            Durasi
                        </th>

                        <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            Lokasi
                        </th>

                        <th class="px-6 py-4 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            Status
                        </th>

                        <th class="px-6 py-4 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody

                    id="stopHistoryTable"

                    class="divide-y divide-slate-100 bg-white"

                >

                    {{-- Render dari database / javascript --}}

                </tbody>

            </table>

        </div>

        {{-- ===================================================== --}}
        {{-- Empty State --}}
        {{-- ===================================================== --}}

        <div

            id="stopHistoryEmpty"

            class="hidden px-6 py-20 text-center"

        >

            <div
                class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100"
            >

                <i
                    class="fa-solid fa-car-side text-2xl text-slate-400"
                ></i>

            </div>

            <h3
                class="mt-6 text-[18px] font-semibold text-slate-900"
            >
                Belum Ada Riwayat Stop
            </h3>

            <p
                class="mt-2 text-[13px] leading-6 text-slate-500"
            >
                Riwayat akan muncul secara otomatis ketika kendaraan
                berhenti sesuai durasi Stop Detection yang telah ditentukan.
            </p>

        </div>

    </section>

</section>