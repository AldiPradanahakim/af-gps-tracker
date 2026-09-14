{{-- ========================================================= --}}
{{-- BATAS KECEPATAN --}}
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
                BATAS KECEPATAN
            </p>

            <h2
                class="mt-2 text-[20px] font-bold text-slate-900"
            >
                Pengaturan Batas Kecepatan
            </h2>

            <p
                class="mt-2 max-w-3xl text-[13px] leading-6 text-slate-500"
            >
                Atur batas kecepatan kendaraan (overspeed) serta tentukan media
                notifikasi yang akan digunakan ketika kendaraan melebihi batas
                kecepatan yang ditentukan.
            </p>

        </div>

        <button

            id="editSpeedSettingButton"

            type="button"

            class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 text-[12px] font-semibold text-slate-700 transition hover:border-blue-300 hover:text-blue-600"

        >

            <i
                class="fa-solid fa-pen-to-square"
            ></i>

            Ubah Pengaturan

        </button>

    </div>

    {{-- ===================================================== --}}
    {{-- READ MODE --}}
    {{-- ===================================================== --}}

    <div

        id="speedSettingReadContainer"

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
                    Status Batas Kecepatan
                </div>

                <div
                    class="text-center text-slate-400"
                >
                    :
                </div>

                <div>

                    @if($speedSetting?->enabled)

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
                {{-- Speed Limit --}}
                {{-- ===================================================== --}}

                <div
                    class="text-slate-500"
                >
                    Batas Kecepatan
                </div>

                <div
                    class="text-center text-slate-400"
                >
                    :
                </div>

                <div
                    class="font-semibold text-slate-900"
                >
                    {{ $speedSetting?->limit_kmh ?? '-' }}
                    Km/Jam
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

                    @if($speedSetting?->email_notification)

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

                    @if($speedSetting?->whatsapp_notification)

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

        id="speedSettingStatusCard"

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
                                Batas Kecepatan
                            </p>

                            <h4
                                class="mt-2 text-[18px] font-bold text-slate-900"
                            >
                                {{ $speedSetting?->enabled ? 'ON' : 'OFF' }}
                            </h4>

                        </div>

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50"
                        >

                            <i
                                class="fa-solid fa-gauge-high text-blue-600"
                            ></i>

                        </div>

                    </div>

                </div>

                {{-- ===================================================== --}}
                {{-- Speed Limit --}}
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
                                Limit Kecepatan
                            </p>

                            <h4
                                class="mt-2 text-[18px] font-bold text-slate-900"
                            >
                                {{ $speedSetting?->limit_kmh ?? '-' }}

                                <span
                                    class="text-[12px] font-medium text-slate-500"
                                >
                                    Km/Jam
                                </span>

                            </h4>

                        </div>

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50"
                        >

                            <i
                                class="fa-solid fa-triangle-exclamation text-amber-600"
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
                                {{ $speedSetting?->email_notification ? 'ON' : 'OFF' }}
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
                                {{ $speedSetting?->whatsapp_notification ? 'ON' : 'OFF' }}
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

        id="speedSettingEditContainer"

        class="hidden p-6"

    >

            <form

                id="speedSettingForm"

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
                                Batas Kecepatan
                            </h3>

                            <p
                                class="mt-1 text-[12px] leading-5 text-slate-500"
                            >
                                Aktifkan atau nonaktifkan fitur peringatan batas kecepatan (overspeed).
                            </p>

                        </div>

                        <label class="relative inline-flex cursor-pointer items-center">

                            <input

                                id="speedSettingEnabled"

                                name="enabled"

                                type="checkbox"

                                class="peer sr-only"

                                @checked($speedSetting?->enabled)

                            >

                            <div
                                class="h-7 w-12 rounded-full bg-slate-300 transition peer-checked:bg-blue-600 after:absolute after:left-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all peer-checked:after:translate-x-5"
                            ></div>

                        </label>

                    </div>

                </div>

                {{-- ===================================================== --}}
                {{-- Speed Limit --}}
                {{-- ===================================================== --}}

                <div>

                    <label
                        class="mb-2 block text-[12px] font-semibold text-slate-700"
                    >
                        Batas Kecepatan
                    </label>

                    <div
                        class="flex items-center gap-3"
                    >

                        <input

                            id="speedLimitKmh"

                            name="limit_kmh"

                            type="number"

                            min="1"

                            max="300"

                            value="{{ $speedSetting?->limit_kmh ?? 80 }}"

                            class="h-11 w-40 rounded-xl border border-slate-300 px-4 text-center text-[14px] font-semibold outline-none transition focus:border-blue-500"

                        >

                        <span
                            class="text-[13px] font-medium text-slate-500"
                        >
                            Km/Jam
                        </span>

                    </div>

                    <p
                        class="mt-2 text-[12px] text-slate-400"
                    >
                        Kendaraan dianggap melebihi batas kecepatan apabila berada
                        di atas <strong>{{ $speedSetting?->limit_kmh ?? 80 }} km/jam</strong>.
                    </p>

                </div>

                {{-- ===================================================== --}}
                {{-- Notification --}}
                {{-- ===================================================== --}}

                <div
                    class="space-y-4"
                >

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
                                Mengirim email ketika kendaraan melebihi batas kecepatan.
                            </p>

                        </div>

                        <label class="relative inline-flex cursor-pointer items-center">

                            <input

                                id="speedEmailNotification"

                                name="email_notification"

                                type="checkbox"

                                class="peer sr-only"

                                @checked($speedSetting?->email_notification)

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
                                Mengirim WhatsApp ketika kendaraan melebihi batas kecepatan.
                            </p>

                        </div>

                        <label class="relative inline-flex cursor-pointer items-center">

                            <input

                                id="speedWhatsappNotification"

                                name="whatsapp_notification"

                                type="checkbox"

                                class="peer sr-only"

                                @checked($speedSetting?->whatsapp_notification)

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

                        id="cancelSpeedSettingEdit"

                        type="button"

                        class="rounded-xl border border-slate-300 px-5 py-2.5 text-[13px] font-semibold text-slate-700 transition hover:bg-slate-100"

                    >

                        Batal

                    </button>

                    <button

                        id="saveSpeedSetting"

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
