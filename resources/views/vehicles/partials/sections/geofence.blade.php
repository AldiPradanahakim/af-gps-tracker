<div
    class="space-y-6"
>

    {{-- ========================================================= --}}
    {{-- Header --}}
    {{-- ========================================================= --}}

    <section
        class="overflow-hidden rounded-[20px] border border-slate-200 bg-white vehicle-panel-shadow"
    >

        <div
            class="px-6 py-5"
        >

            <p
                class="text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-400"
            >
                GEOFENCE
            </p>

            <h2
                class="mt-2 text-[20px] font-bold text-slate-900"
            >
                Kelola Geofence Kendaraan
            </h2>

            <p
                class="mt-2 max-w-3xl text-[13px] leading-6 text-slate-500"
            >
                Setiap kendaraan hanya dapat memiliki satu Geofence Radius,
                satu Geofence Administratif, dan satu Geofence Poligon.
                Seluruh pengaturan dilakukan langsung pada halaman ini.
            </p>

            {{-- ========================================================= --}}
            {{-- Saring Tampilan Peta --}}
            {{-- ========================================================= --}}

            <div
                class="mt-5 flex flex-wrap items-center gap-5 border-t border-slate-100 pt-4"
            >

                <span class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">
                    Tampilkan di Peta
                </span>

                <label class="flex cursor-pointer items-center gap-2 text-[13px] text-slate-700">
                    <input id="toggleAllGeofenceMap" type="checkbox" checked class="h-4 w-4 rounded border-slate-300 text-blue-600">
                    Semua
                </label>

                <label class="flex cursor-pointer items-center gap-2 text-[13px] text-slate-700">
                    <input id="toggleRadiusMap" type="checkbox" checked class="h-4 w-4 rounded border-slate-300 text-blue-600">
                    Radius
                </label>

                <label class="flex cursor-pointer items-center gap-2 text-[13px] text-slate-700">
                    <input id="toggleAdministrativeMap" type="checkbox" checked class="h-4 w-4 rounded border-slate-300 text-emerald-600">
                    Administratif
                </label>

                <label class="flex cursor-pointer items-center gap-2 text-[13px] text-slate-700">
                    <input id="togglePolygonMap" type="checkbox" checked class="h-4 w-4 rounded border-slate-300 text-violet-600">
                    Poligon
                </label>

            </div>

        </div>

    </section>

    {{-- ========================================================= --}}
    {{-- Notifikasi Geofence --}}
    {{-- ========================================================= --}}

    <section
        class="overflow-hidden rounded-[20px] border border-slate-200 bg-white vehicle-panel-shadow"
    >

        <div class="border-b border-slate-100 px-6 py-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-400">Notifikasi</p>
            <h2 class="mt-2 text-[18px] font-bold text-slate-900">Notifikasi Geofence</h2>
            <p class="mt-1 text-[13px] text-slate-500">Kirim pemberitahuan ketika kendaraan keluar dari area Geofence.</p>
        </div>

        <div class="space-y-4 p-6">

            {{-- ============================== --}}
            {{-- Sistem (selalu aktif) --}}
            {{-- ============================== --}}

            <div class="flex items-center justify-between rounded-2xl border border-slate-200 p-4">
                <div>
                    <h4 class="text-[13px] font-semibold text-slate-900">Notifikasi Sistem</h4>
                    <p class="mt-1 text-[12px] text-slate-500">
                        Menampilkan notifikasi pada dasbor aplikasi.
                        Selalu aktif dan tidak dapat dinonaktifkan.
                    </p>
                </div>
                <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-[12px] font-semibold text-emerald-600">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    Selalu Aktif
                </span>
            </div>

            {{-- ============================== --}}
            {{-- Email --}}
            {{-- ============================== --}}

            <div class="flex items-center justify-between rounded-2xl border border-slate-200 p-4">
                <div>
                    <h4 class="text-[13px] font-semibold text-slate-900">Notifikasi Email</h4>
                    <p class="mt-1 text-[12px] text-slate-500">
                        Mengirim email ketika kendaraan keluar dari area Geofence.
                    </p>
                </div>
                <label class="relative inline-flex cursor-pointer items-center">
                    <input
                        id="geofenceEmailNotification"
                        type="checkbox"
                        class="peer sr-only"
                        @checked($notificationSetting?->email_notification)
                    >
                    <div class="h-7 w-12 rounded-full bg-slate-300 transition peer-checked:bg-blue-600 after:absolute after:left-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>

            {{-- ============================== --}}
            {{-- WhatsApp --}}
            {{-- ============================== --}}

            <div class="flex items-center justify-between rounded-2xl border border-slate-200 p-4">
                <div>
                    <h4 class="text-[13px] font-semibold text-slate-900">Notifikasi WhatsApp</h4>
                    <p class="mt-1 text-[12px] text-slate-500">
                        Mengirim WhatsApp ketika kendaraan keluar dari area Geofence.
                    </p>
                </div>
                <label class="relative inline-flex cursor-pointer items-center">
                    <input
                        id="geofenceWhatsappNotification"
                        type="checkbox"
                        class="peer sr-only"
                        @checked($notificationSetting?->whatsapp_notification)
                    >
                    <div class="h-7 w-12 rounded-full bg-slate-300 transition peer-checked:bg-blue-600 after:absolute after:left-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>

            {{-- ============================== --}}
            {{-- Pengingat "Masih di Luar Area" --}}
            {{-- ============================== --}}

            <div class="rounded-2xl border border-slate-200 p-4">

                <div class="flex items-start justify-between gap-4">

                    <div>
                        <h4 class="text-[13px] font-semibold text-slate-900">Pengingat Keluar Geofence</h4>
                        <p class="mt-1 text-[12px] leading-5 text-slate-500">
                            Terus mengirim pemberitahuan selama kendaraan masih
                            berada di luar area, bukan hanya sekali saat keluar.
                            Dikirim per area, mengikuti pengaturan Email/WhatsApp di atas.
                        </p>
                    </div>

                    <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                        <input
                            id="geofenceRepeatEnabled"
                            type="checkbox"
                            class="peer sr-only"
                            @checked($geofenceSetting?->repeat_enabled)
                        >
                        <div class="h-7 w-12 rounded-full bg-slate-300 transition peer-checked:bg-blue-600 after:absolute after:left-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all peer-checked:after:translate-x-5"></div>
                    </label>

                </div>

                <div
                    id="geofenceRepeatOptions"
                    class="{{ $geofenceSetting?->repeat_enabled ? '' : 'hidden' }} mt-4 border-t border-slate-100 pt-4"
                >

                    <label class="mb-2 block text-[12px] font-semibold text-slate-700">
                        Jeda Antar Pengingat (Menit)
                    </label>

                    <div class="flex flex-wrap items-center gap-3">

                        <input
                            id="geofenceRepeatMinutes"
                            type="number"
                            min="{{ $geofenceSetting?->min_minutes ?? 5 }}"
                            max="{{ $geofenceSetting?->max_minutes ?? 180 }}"
                            value="{{ $geofenceSetting?->repeat_minutes ?? 15 }}"
                            class="h-11 w-32 rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-blue-500"
                        >

                        <button
                            id="saveGeofenceRepeatSetting"
                            type="button"
                            class="h-11 rounded-xl bg-blue-600 px-5 text-[13px] font-semibold text-white transition hover:bg-blue-700"
                        >
                            Simpan
                        </button>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {{-- ========================================================= --}}
    {{-- CARD --}}
    {{-- ========================================================= --}}

    <div
        class="grid grid-cols-1 gap-6 xl:grid-cols-3"
    >

        {{-- ===================================================== --}}
        {{-- Radius --}}
        {{-- ===================================================== --}}

        <section

            id="radiusGeofenceCard"

            class="overflow-hidden rounded-[20px] border border-slate-200 bg-white vehicle-panel-shadow"

        >

            {{-- ========================================================= --}}
            {{-- Radius Card --}}
            {{-- ========================================================= --}}

            <div
                class="border-b border-slate-200 px-5 py-5"
            >

                <div
                    class="flex items-center justify-between"
                >

                    <div>

                        <p
                            class="text-[10px] font-semibold uppercase tracking-[0.25em] text-blue-600"
                        >
                            RADIUS
                        </p>

                        <h3
                            class="mt-2 text-[18px] font-bold text-slate-900"
                        >
                            Geofence Radius
                        </h3>

                    </div>

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50"
                    >

                        <i
                            class="fa-solid fa-location-dot text-blue-600"
                        ></i>

                    </div>

                </div>

            </div>

            {{-- ========================================================= --}}
            {{-- READ MODE --}}
            {{-- ========================================================= --}}

            <div
                id="radiusReadContainer"
            >

                @if($radius)

                    <div
                        class="space-y-5 p-5"
                    >

                        <div
                            class="space-y-4"
                        >

                            <div class="grid grid-cols-[110px_15px_1fr]">

                                <span class="text-[12px] text-slate-500">
                                    Nama
                                </span>

                                <span class="text-slate-400">
                                    :
                                </span>

                                <span class="font-semibold text-slate-900">
                                    {{ $radius->name }}
                                </span>

                            </div>

                            <div class="grid grid-cols-[110px_15px_1fr]">

                                <span class="text-[12px] text-slate-500">
                                    Radius
                                </span>

                                <span class="text-slate-400">
                                    :
                                </span>

                                <span class="font-semibold text-slate-900">
                                    {{ number_format($radius->config['radius'] ?? 0) }} Meter
                                </span>

                            </div>

                            <div class="grid grid-cols-[110px_15px_1fr]">

                                <span class="text-[12px] text-slate-500">
                                    Status
                                </span>

                                <span class="text-slate-400">
                                    :
                                </span>

                                <span>

                                    @if($radius->status)

                                        <span
                                            class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold text-emerald-700"
                                        >
                                            Aktif
                                        </span>

                                    @else

                                        <span
                                            class="rounded-full bg-red-100 px-3 py-1 text-[11px] font-semibold text-red-700"
                                        >
                                            Nonaktif
                                        </span>

                                    @endif

                                </span>

                            </div>

                            <div class="grid grid-cols-[110px_15px_1fr]">

                                <span class="text-[12px] text-slate-500">
                                    Dibuat
                                </span>

                                <span class="text-slate-400">
                                    :
                                </span>

                                <span class="font-semibold text-slate-900">
                                    {{ optional($radius->created_at)->format('d M Y H:i') }}
                                </span>

                            </div>

                        </div>

                        <div
                            class="grid grid-cols-2 gap-3 pt-2"
                        >

                            <button

                                id="editRadiusButton"

                                type="button"

                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 py-2.5 text-[12px] font-semibold text-blue-600 transition hover:bg-blue-100"

                            >

                                <i
                                    class="fa-solid fa-pen"
                                ></i>

                                Ubah

                            </button>

                            <button

                                id="deleteRadiusButton"

                                type="button"

                                data-id="{{ $radius->id }}"

                                data-name="{{ $radius->name }}"

                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 py-2.5 text-[12px] font-semibold text-red-600 transition hover:bg-red-100"

                            >

                                <i
                                    class="fa-solid fa-trash"
                                ></i>

                                Hapus

                            </button>

                        </div>

                    </div>

                @else

                    {{-- ================================================ --}}
                    {{-- EMPTY STATE --}}
                    {{-- ================================================ --}}

                    <div
                        class="flex flex-col items-center justify-center px-6 py-14 text-center"
                    >

                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-50"
                        >

                            <i
                                class="fa-solid fa-location-dot text-2xl text-blue-600"
                            ></i>

                        </div>

                        <h4
                            class="mt-5 text-[15px] font-semibold text-slate-900"
                        >
                            Radius belum tersedia
                        </h4>

                        <p
                            class="mt-2 text-[12px] leading-6 text-slate-500"
                        >
                            Kendaraan ini belum memiliki Geofence Radius.
                        </p>

                        <button

                            id="createRadiusButton"

                            type="button"

                            class="mt-6 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-[12px] font-semibold text-white transition hover:bg-blue-700"

                        >

                            <i
                                class="fa-solid fa-plus"
                            ></i>

                            Tambah Radius

                        </button>

                    </div>

                @endif

            </div>

            {{-- ========================================================= --}}
            {{-- EDIT MODE --}}
            {{-- PART 5 --}}
            {{-- ========================================================= --}}

            <div

                id="radiusEditContainer"

                class="hidden p-5"

            >

                <form
                    id="editRadiusForm"
                    class="space-y-5"
                >

                    <input id="editRadiusId" type="hidden" value="{{ $radius->id ?? '' }}">

                    {{-- Nama --}}

                    <div>

                        <label
                            class="mb-2 block text-[12px] font-medium text-slate-700"
                        >
                            Nama Radius
                        </label>

                        <input

                            id="editRadiusName"

                            name="name"

                            type="text"

                            value="{{ $radius->name ?? '' }}"

                            class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-blue-500"

                        >

                    </div>

                    {{-- Sumber Titik --}}
                    
                    <div>
                    
                        <label class="mb-2 block text-[12px] font-medium text-slate-700">
                            Titik Pusat
                        </label>
                    
                        <select
                            id="editRadiusSource"
                            class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-blue-500"
                        >
                            <option value="keep_current">Pertahankan Titik Saat Ini</option>
                            <option value="home_location">Lokasi Rumah</option>
                            <option value="current_location">Lokasi GPS Terakhir</option>
                        </select>

                        <p class="mt-2 text-[11px] leading-5 text-slate-500">
                            Titik pusat saat ini diambil dari
                            <b>{{ match($radius->config['source'] ?? null) {
                                'home_location' => 'Lokasi Rumah',
                                'current_location' => 'Lokasi GPS Terakhir',
                                'manual' => 'titik manual',
                                default => 'titik tersimpan',
                            } }}</b>.
                            Titik ini tidak berpindah sendiri. Pilih ulang sumbernya
                            kalau ingin memakai koordinat terbaru.
                        </p>

                    </div>

                    {{-- Radius --}}

                    <div>

                        <label
                            class="mb-2 block text-[12px] font-medium text-slate-700"
                        >
                            Radius (Meter)
                        </label>

                        <input

                            id="editRadiusValue"

                            name="radius"

                            type="number"

                            value="{{ $radius->config['radius'] ?? '' }}"

                            class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-blue-500"

                        >

                    </div>

                    {{-- Status --}}

                    <div>

                        <label
                            class="mb-2 block text-[12px] font-medium text-slate-700"
                        >
                            Status
                        </label>

                        <select

                            id="editRadiusStatus"

                            name="status"

                            class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none"

                        >

                            <option value="1" @selected(($radius->status ?? true))>
                                Aktif
                            </option>

                            <option value="0" @selected(! ($radius->status ?? true))>
                                Nonaktif
                            </option>

                        </select>

                    </div>

                    {{-- Action --}}

                    <div
                        class="grid grid-cols-2 gap-3 pt-2"
                    >

                        <button

                            id="cancelRadiusEdit"

                            type="button"

                            class="rounded-xl border border-slate-300 py-2.5 text-[12px] font-semibold"

                        >

                            Batal

                        </button>

                        <button

                            id="saveRadiusEdit"

                            type="submit"

                            class="rounded-xl bg-blue-600 py-2.5 text-[12px] font-semibold text-white"

                        >

                            <i class="fa-solid fa-floppy-disk mr-2"></i>

                            Simpan

                        </button>

                    </div>

                </form>

            </div>

        </section>

        {{-- ===================================================== --}}
        {{-- Administratif --}}
        {{-- ===================================================== --}}

        <section

            id="administrativeGeofenceCard"

            class="overflow-hidden rounded-[20px] border border-slate-200 bg-white vehicle-panel-shadow"

        >

            {{-- ========================================================= --}}
            {{-- Administratif Card --}}
            {{-- ========================================================= --}}

            <div
                class="border-b border-slate-200 px-5 py-5"
            >

                <div
                    class="flex items-center justify-between"
                >

                    <div>

                        <p
                            class="text-[10px] font-semibold uppercase tracking-[0.25em] text-emerald-600"
                        >
                            ADMINISTRATIVE
                        </p>

                        <h3
                            class="mt-2 text-[18px] font-bold text-slate-900"
                        >
                            Geofence Administratif
                        </h3>

                    </div>

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50"
                    >

                        <i
                            class="fa-solid fa-map-location-dot text-emerald-600"
                        ></i>

                    </div>

                </div>

            </div>

            {{-- ========================================================= --}}
            {{-- READ MODE --}}
            {{-- ========================================================= --}}

            <div
                id="administrativeReadContainer"
            >

                @if($administrative)

                    <div
                        class="space-y-5 p-5"
                    >

                        <div
                            class="space-y-4"
                        >

                            <div class="grid grid-cols-[110px_15px_1fr]">

                                <span class="text-[12px] text-slate-500">
                                    Nama
                                </span>

                                <span class="text-slate-400">
                                    :
                                </span>

                                <span class="font-semibold text-slate-900">
                                    {{ $administrative->name }}
                                </span>

                            </div>

                            <div class="grid grid-cols-[110px_15px_1fr]">

                                <span class="text-[12px] text-slate-500">
                                    Wilayah
                                </span>

                                <span class="text-slate-400">
                                    :
                                </span>

                                <span class="font-semibold text-slate-900">
                                    {{ $administrative->config['display_name'] ?? '-' }}
                                </span>

                            </div>

                            <div class="grid grid-cols-[110px_15px_1fr]">

                                <span class="text-[12px] text-slate-500">
                                    Tipe Wilayah
                                </span>

                                <span class="text-slate-400">
                                    :
                                </span>

                                <span class="font-semibold text-slate-900">
                                    {{ $administrative->config['administrative_type'] === 'village' ? 'Kelurahan' : 'Kecamatan' }}
                                </span>

                            </div>

                            <div class="grid grid-cols-[110px_15px_1fr]">

                                <span class="text-[12px] text-slate-500">
                                    Status
                                </span>

                                <span class="text-slate-400">
                                    :
                                </span>

                                <span>

                                    @if($administrative->status)

                                        <span
                                            class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold text-emerald-700"
                                        >
                                            Aktif
                                        </span>

                                    @else

                                        <span
                                            class="rounded-full bg-red-100 px-3 py-1 text-[11px] font-semibold text-red-700"
                                        >
                                            Nonaktif
                                        </span>

                                    @endif

                                </span>

                            </div>

                            <div class="grid grid-cols-[110px_15px_1fr]">

                                <span class="text-[12px] text-slate-500">
                                    Dibuat
                                </span>

                                <span class="text-slate-400">
                                    :
                                </span>

                                <span class="font-semibold text-slate-900">
                                    {{ optional($administrative->created_at)->format('d M Y H:i') }}
                                </span>

                            </div>

                        </div>

                        <div
                            class="grid grid-cols-2 gap-3 pt-2"
                        >

                            <button

                                id="editAdministrativeButton"

                                type="button"

                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 py-2.5 text-[12px] font-semibold text-blue-600 transition hover:bg-blue-100"

                            >

                                <i class="fa-solid fa-pen"></i>

                                Ubah

                            </button>

                            <button

                                id="deleteAdministrativeButton"

                                type="button"

                                data-id="{{ $administrative->id }}"

                                data-name="{{ $administrative->name }}"

                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 py-2.5 text-[12px] font-semibold text-red-600 transition hover:bg-red-100"

                            >

                                <i class="fa-solid fa-trash"></i>

                                Hapus

                            </button>

                        </div>

                    </div>

                @else

                    {{-- ===================================================== --}}
                    {{-- EMPTY STATE --}}
                    {{-- ===================================================== --}}

                    <div
                        class="flex flex-col items-center justify-center px-6 py-14 text-center"
                    >

                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50"
                        >

                            <i
                                class="fa-solid fa-map-location-dot text-2xl text-emerald-600"
                            ></i>

                        </div>

                        <h4
                            class="mt-5 text-[15px] font-semibold text-slate-900"
                        >
                            Administratif belum tersedia
                        </h4>

                        <p
                            class="mt-2 text-[12px] leading-6 text-slate-500"
                        >
                            Kendaraan ini belum memiliki Geofence Administratif.
                        </p>

                        <button

                            id="createAdministrativeButton"

                            type="button"

                            class="mt-6 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-[12px] font-semibold text-white transition hover:bg-emerald-700"

                        >

                            <i class="fa-solid fa-plus"></i>

                            Tambah Administratif

                        </button>

                    </div>

                @endif

            </div>

            {{-- ========================================================= --}}
            {{-- EDIT MODE --}}
            <div

                id="administrativeEditContainer"

                class="hidden p-5"

            >

                <form

                    id="editAdministrativeForm"

                    class="space-y-5"

                >

                    <input id="editAdministrativeId" type="hidden" value="{{ $administrative->id ?? '' }}">
                    <input id="editAdministrativeGeojson" type="hidden" value="">
                    <input id="editAdministrativeDisplayName" type="hidden" value="{{ $administrative->config['display_name'] ?? '' }}">
                    <input id="editAdministrativeAreaType" type="hidden" value="{{ $administrative->config['administrative_type'] ?? '' }}">

                    {{-- Nama --}}

                    <div>

                        <label
                            class="mb-2 block text-[12px] font-medium text-slate-700"
                        >
                            Nama Administratif
                        </label>

                        <input

                            id="editAdministrativeName"

                            name="name"

                            type="text"

                            value="{{ $administrative->name ?? '' }}"

                            class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-emerald-500"

                        >

                    </div>

                    {{-- Cari Wilayah --}}

                    <div>

                        <label
                            class="mb-2 block text-[12px] font-medium text-slate-700"
                        >
                            Wilayah (opsional, biarkan kosong jika tidak diubah)
                        </label>

                        <div class="relative">

                            <input

                                id="editAdministrativeSearch"

                                type="text"

                                autocomplete="off"

                                value="{{ $administrative->config['display_name'] ?? '' }}"

                                placeholder="Cari kecamatan atau kelurahan..."

                                class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-emerald-500"

                            >

                            <div
                                id="editAdministrativeResult"
                                class="absolute left-0 right-0 top-full z-[100] mt-2 hidden max-h-64 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg"
                            ></div>

                        </div>

                    </div>

                    {{-- Status --}}

                    <div>

                        <label
                            class="mb-2 block text-[12px] font-medium text-slate-700"
                        >
                            Status
                        </label>

                        <select

                            id="editAdministrativeStatus"

                            name="status"

                            class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none"

                        >

                            <option value="1" @selected(($administrative->status ?? true))>
                                Aktif
                            </option>

                            <option value="0" @selected(! ($administrative->status ?? true))>
                                Nonaktif
                            </option>

                        </select>

                    </div>

                    {{-- Informasi --}}

                    <div
                        class="rounded-xl border border-emerald-200 bg-emerald-50 p-4"
                    >

                        <div
                            class="flex items-start gap-3"
                        >

                            <i
                                class="fa-solid fa-circle-info mt-0.5 text-emerald-600"
                            ></i>

                            <p
                                class="text-[12px] leading-6 text-emerald-700"
                            >
                                Perubahan wilayah administrasi akan memperbarui
                                batas geofence sesuai wilayah yang dipilih.
                            </p>

                        </div>

                    </div>

                    {{-- Action --}}

                    <div
                        class="grid grid-cols-2 gap-3 pt-2"
                    >

                        <button

                            id="cancelAdministrativeEdit"

                            type="button"

                            class="rounded-xl border border-slate-300 py-2.5 text-[12px] font-semibold"

                        >

                            Batal

                        </button>

                        <button

                            id="saveAdministrativeEdit"

                            type="submit"

                            class="rounded-xl bg-emerald-600 py-2.5 text-[12px] font-semibold text-white"

                        >

                            <i class="fa-solid fa-floppy-disk mr-2"></i>

                            Simpan

                        </button>

                    </div>

                </form>

            </div>

        </section>

        {{-- ===================================================== --}}
        {{-- Poligon --}}
        {{-- ===================================================== --}}

        <section

            id="polygonGeofenceCard"

            class="overflow-hidden rounded-[20px] border border-slate-200 bg-white vehicle-panel-shadow"

        >

            {{-- ========================================================= --}}
            {{-- Poligon Card --}}
            {{-- ========================================================= --}}

            <div
                class="border-b border-slate-200 px-5 py-5"
            >

                <div
                    class="flex items-center justify-between"
                >

                    <div>

                        <p
                            class="text-[10px] font-semibold uppercase tracking-[0.25em] text-violet-600"
                        >
                            POLYGON
                        </p>

                        <h3
                            class="mt-2 text-[18px] font-bold text-slate-900"
                        >
                            Geofence Poligon
                        </h3>

                    </div>

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50"
                    >

                        <i
                            class="fa-solid fa-draw-polygon text-violet-600"
                        ></i>

                    </div>

                </div>

            </div>

            {{-- ========================================================= --}}
            {{-- READ MODE --}}
            {{-- ========================================================= --}}

            <div
                id="polygonReadContainer"
            >

                @if($polygon)

                    <div
                        class="space-y-5 p-5"
                    >

                        <div
                            class="space-y-4"
                        >

                            {{-- Nama --}}

                            <div
                                class="grid grid-cols-[110px_15px_1fr]"
                            >

                                <span
                                    class="text-[12px] text-slate-500"
                                >
                                    Nama
                                </span>

                                <span
                                    class="text-slate-400"
                                >
                                    :
                                </span>

                                <span
                                    class="font-semibold text-slate-900"
                                >
                                    {{ $polygon->name }}
                                </span>

                            </div>

                            {{-- Jumlah Titik --}}

                            <div
                                class="grid grid-cols-[110px_15px_1fr]"
                            >

                                <span
                                    class="text-[12px] text-slate-500"
                                >
                                    Jumlah Titik
                                </span>

                                <span
                                    class="text-slate-400"
                                >
                                    :
                                </span>

                                <span
                                    class="font-semibold text-slate-900"
                                >
                                    {{ count($polygon->config['geometry']['coordinates'][0] ?? []) }}
                                </span>

                            </div>

                            {{-- Status --}}

                            <div
                                class="grid grid-cols-[110px_15px_1fr]"
                            >

                                <span
                                    class="text-[12px] text-slate-500"
                                >
                                    Status
                                </span>

                                <span
                                    class="text-slate-400"
                                >
                                    :
                                </span>

                                <span>

                                    @if($polygon->status)

                                        <span
                                            class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold text-emerald-700"
                                        >
                                            Aktif
                                        </span>

                                    @else

                                        <span
                                            class="rounded-full bg-red-100 px-3 py-1 text-[11px] font-semibold text-red-700"
                                        >
                                            Nonaktif
                                        </span>

                                    @endif

                                </span>

                            </div>

                            {{-- Dibuat --}}

                            <div
                                class="grid grid-cols-[110px_15px_1fr]"
                            >

                                <span
                                    class="text-[12px] text-slate-500"
                                >
                                    Dibuat
                                </span>

                                <span
                                    class="text-slate-400"
                                >
                                    :
                                </span>

                                <span
                                    class="font-semibold text-slate-900"
                                >
                                    {{ optional($polygon->created_at)->format('d M Y H:i') }}
                                </span>

                            </div>

                        </div>

                        {{-- ========================================= --}}
                        {{-- Action --}}
                        {{-- ========================================= --}}

                        <div
                            class="grid grid-cols-2 gap-3 pt-2"
                        >

                            <button

                                id="editPolygonButton"

                                type="button"

                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 py-2.5 text-[12px] font-semibold text-blue-600 transition hover:bg-blue-100"

                            >

                                <i
                                    class="fa-solid fa-pen"
                                ></i>

                                Ubah

                            </button>

                            <button

                                id="deletePolygonButton"

                                type="button"

                                data-id="{{ $polygon->id }}"

                                data-name="{{ $polygon->name }}"

                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 py-2.5 text-[12px] font-semibold text-red-600 transition hover:bg-red-100"

                            >

                                <i
                                    class="fa-solid fa-trash"
                                ></i>

                                Hapus

                            </button>

                        </div>

                    </div>

                @else

                    {{-- ========================================= --}}
                    {{-- EMPTY --}}
                    {{-- ========================================= --}}

                    <div
                        class="flex flex-col items-center justify-center px-6 py-14 text-center"
                    >

                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-full bg-violet-50"
                        >

                            <i
                                class="fa-solid fa-draw-polygon text-2xl text-violet-600"
                            ></i>

                        </div>

                        <h4
                            class="mt-5 text-[15px] font-semibold text-slate-900"
                        >
                            Poligon belum tersedia
                        </h4>

                        <p
                            class="mt-2 text-[12px] leading-6 text-slate-500"
                        >
                            Kendaraan ini belum memiliki Geofence Poligon.
                        </p>

                        <button

                            id="createPolygonButton"

                            type="button"

                            class="mt-6 inline-flex items-center gap-2 rounded-xl bg-violet-600 px-5 py-2.5 text-[12px] font-semibold text-white transition hover:bg-violet-700"

                        >

                            <i
                                class="fa-solid fa-plus"
                            ></i>

                            Tambah Poligon

                        </button>

                    </div>

                @endif

            </div>

            {{-- ========================================================= --}}
            {{-- EDIT MODE --}}
            {{-- ========================================================= --}}

            <div

                id="polygonEditContainer"

                class="hidden p-5"

            >

                <form

                    id="editPolygonForm"

                    class="space-y-5"

                >

                    <input id="editPolygonId" type="hidden" value="{{ $polygon->id ?? '' }}">
                    <input id="editPolygonGeojson" type="hidden" value="">

                    {{-- Nama Poligon --}}

                    <div>

                        <label
                            class="mb-2 block text-[12px] font-medium text-slate-700"
                        >
                            Nama Poligon
                        </label>

                        <input

                            id="editPolygonName"

                            name="name"

                            type="text"

                            value="{{ $polygon->name ?? '' }}"

                            class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-violet-500"

                        >

                    </div>

                    {{-- Status --}}

                    <div>

                        <label
                            class="mb-2 block text-[12px] font-medium text-slate-700"
                        >
                            Status
                        </label>

                        <select

                            id="editPolygonStatus"

                            name="status"

                            class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-violet-500"

                        >

                            <option value="1" @selected(($polygon->status ?? true))>
                                Aktif
                            </option>

                            <option value="0" @selected(! ($polygon->status ?? true))>
                                Nonaktif
                            </option>

                        </select>

                    </div>

                    {{-- Informasi Poligon --}}

                    <div
                        class="rounded-xl border border-violet-200 bg-violet-50 p-4"
                    >

                        <div
                            class="flex items-start gap-3"
                        >

                            <i
                                class="fa-solid fa-circle-info mt-0.5 text-violet-600"
                            ></i>

                            <p
                                class="text-[12px] leading-6 text-violet-700"
                            >
                                Untuk mengubah bentuk Poligon,
                                klik tombol <strong>Ubah Area Poligon</strong>,
                                kemudian edit titik-titik Poligon langsung pada peta.
                            </p>

                        </div>

                    </div>

                    {{-- Area Poligon --}}

                    <button

                        id="changePolygonArea"

                        type="button"

                        class="flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-violet-200 bg-violet-50 text-[12px] font-semibold text-violet-600 transition hover:bg-violet-100"

                    >

                        <i
                            class="fa-solid fa-draw-polygon"
                        ></i>

                        Ubah Area Poligon

                    </button>

                    {{-- Action --}}

                    <div
                        class="grid grid-cols-2 gap-3 pt-2"
                    >

                        <button

                            id="cancelPolygonEdit"

                            type="button"

                            class="rounded-xl border border-slate-300 py-2.5 text-[12px] font-semibold transition hover:bg-slate-100"

                        >

                            Batal

                        </button>

                        <button

                            id="savePolygonEdit"

                            type="submit"

                            class="rounded-xl bg-violet-600 py-2.5 text-[12px] font-semibold text-white transition hover:bg-violet-700"

                        >

                            <i
                                class="fa-solid fa-floppy-disk mr-2"
                            ></i>

                            Simpan

                        </button>

                    </div>

                </form>

            </div>

        </section>

    </div>

    {{-- ========================================================= --}}
    {{-- RIWAYAT MASUK/KELUAR GEOFENCE --}}
    {{-- ========================================================= --}}

    <section
        class="overflow-hidden rounded-[20px] border border-slate-200 bg-white vehicle-panel-shadow"
    >

        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">

            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-400">Riwayat</p>
                <h2 class="mt-2 text-[18px] font-bold text-slate-900">Riwayat Masuk &amp; Keluar Geofence</h2>
                <p class="mt-1 max-w-2xl text-[13px] leading-6 text-slate-500">
                    Setiap perpindahan dicatat per area, lengkap dengan lamanya
                    kendaraan berada pada status sebelumnya. Pengingat berulang
                    tidak dicatat di sini.
                </p>
            </div>

            <button
                id="geofenceHistoryRefreshButton"
                type="button"
                class="inline-flex h-10 items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-[13px] font-semibold text-slate-700 transition hover:border-blue-500 hover:text-blue-600"
            >
                <i class="fa-solid fa-rotate"></i>
                Muat Ulang
            </button>

        </div>

        {{-- Ringkasan --}}

        <div class="grid grid-cols-2 gap-px bg-slate-100 sm:grid-cols-4">

            <div class="bg-white px-5 py-4">
                <p class="text-[11px] font-medium uppercase tracking-[0.14em] text-slate-400">Total Kejadian</p>
                <p id="geofenceHistoryTotal" class="mt-2 text-xl font-semibold text-slate-900">0</p>
            </div>

            <div class="bg-white px-5 py-4">
                <p class="text-[11px] font-medium uppercase tracking-[0.14em] text-slate-400">Keluar</p>
                <p id="geofenceHistoryTotalExit" class="mt-2 text-xl font-semibold text-red-600">0</p>
            </div>

            <div class="bg-white px-5 py-4">
                <p class="text-[11px] font-medium uppercase tracking-[0.14em] text-slate-400">Masuk</p>
                <p id="geofenceHistoryTotalEnter" class="mt-2 text-xl font-semibold text-emerald-600">0</p>
            </div>

            <div class="bg-white px-5 py-4">
                <p class="text-[11px] font-medium uppercase tracking-[0.14em] text-slate-400">Hari Ini</p>
                <p id="geofenceHistoryToday" class="mt-2 text-xl font-semibold text-slate-900">0</p>
            </div>

        </div>

        {{-- Saring --}}

        <div class="flex flex-wrap items-end gap-3 border-t border-slate-100 px-6 py-4">

            <div class="min-w-[150px] flex-1">
                <label class="mb-2 block text-[12px] font-medium text-slate-600">Dari Tanggal</label>
                <input id="geofenceHistoryStartDate" type="date" class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-blue-500">
            </div>

            <div class="min-w-[150px] flex-1">
                <label class="mb-2 block text-[12px] font-medium text-slate-600">Sampai Tanggal</label>
                <input id="geofenceHistoryEndDate" type="date" class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-blue-500">
            </div>

            <div class="min-w-[150px] flex-1">
                <label class="mb-2 block text-[12px] font-medium text-slate-600">Kejadian</label>
                <select id="geofenceHistoryEvent" class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-blue-500">
                    <option value="">Semua</option>
                    <option value="exit">Keluar</option>
                    <option value="enter">Masuk</option>
                </select>
            </div>

            <button
                id="geofenceHistoryApplyButton"
                type="button"
                class="h-11 rounded-xl bg-blue-600 px-5 text-[13px] font-semibold text-white transition hover:bg-blue-700"
            >
                Tampilkan
            </button>

            <button
                id="geofenceHistoryResetButton"
                type="button"
                class="h-11 rounded-xl border border-slate-300 bg-white px-5 text-[13px] font-semibold text-slate-700 transition hover:bg-slate-100"
            >
                Atur Ulang
            </button>

        </div>

        {{-- Daftar --}}

        <div id="geofenceHistoryList" class="divide-y divide-slate-100"></div>

        <div id="geofenceHistoryEmpty" class="hidden px-6 py-14 text-center">

            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
                <i class="fa-solid fa-clock-rotate-left text-2xl text-slate-400"></i>
            </div>

            <h4 class="mt-5 text-[15px] font-semibold text-slate-900">Belum ada riwayat geofence</h4>

            <p class="mt-2 text-[12px] leading-6 text-slate-500">
                Riwayat akan terisi otomatis ketika kendaraan masuk atau keluar
                dari salah satu area Geofence.
            </p>

        </div>

    </section>

    {{-- ========================================================= --}}
    {{-- MODAL --}}
    {{-- Semua modal akan diletakkan di bawah setelah seluruh card --}}
    {{-- ========================================================= --}}

    {{-- ========================================================= --}}
    {{-- CREATE RADIUS MODAL --}}
    {{-- ========================================================= --}}

    <div

        id="createRadiusModal"

        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm"

    >

        <div
            class="w-full max-w-xl overflow-hidden rounded-[24px] bg-white shadow-2xl"
        >

            {{-- ============================================== --}}
            {{-- Header --}}
            {{-- ============================================== --}}

            <div
                class="flex items-center justify-between border-b border-slate-200 px-6 py-5"
            >

                <div>

                    <p
                        class="text-[10px] font-semibold uppercase tracking-[0.25em] text-blue-600"
                    >
                        RADIUS
                    </p>

                    <h2
                        class="mt-2 text-[20px] font-bold text-slate-900"
                    >
                        Tambah Geofence Radius
                    </h2>

                    <p
                        class="mt-1 text-[13px] text-slate-500"
                    >
                        Tambahkan Geofence Radius untuk kendaraan ini.
                    </p>

                </div>

                <button

                    id="closeCreateRadiusModal"

                    type="button"

                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 transition hover:bg-slate-200"

                >

                    <i
                        class="fa-solid fa-xmark"
                    ></i>

                </button>

            </div>

            {{-- ============================================== --}}
            {{-- Body --}}
            {{-- ============================================== --}}

            <form

                id="createRadiusForm"

                class="space-y-5 p-6"

            >

                {{-- Nama --}}

                <div>

                    <label
                        class="mb-2 block text-[12px] font-semibold text-slate-700"
                    >
                        Nama Radius
                    </label>

                    <input

                        id="createRadiusName"

                        name="name"

                        type="text"

                        class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-blue-500"

                        placeholder="Contoh : Rumah"

                    >

                </div>

                {{-- Sumber Titik --}}

                <div>

                    <label
                        class="mb-2 block text-[12px] font-semibold text-slate-700"
                    >
                        Titik Pusat
                    </label>

                    <select

                        id="createRadiusSource"

                        name="radius_source"

                        class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-blue-500"

                    >

                        <option value="home_location">
                            Lokasi Rumah
                        </option>

                        <option value="current_location">
                            Lokasi GPS Terakhir
                        </option>

                    </select>

                </div>

                {{-- Radius --}}

                <div>

                    <label
                        class="mb-2 block text-[12px] font-semibold text-slate-700"
                    >
                        Radius (Meter)
                    </label>

                    <input

                        id="createRadiusValue"

                        name="radius"

                        type="number"

                        min="50"

                        max="50000"

                        value="500"

                        class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-blue-500"

                        placeholder="100"

                    >

                </div>

                {{-- Status --}}

                <div>

                    <label
                        class="mb-2 block text-[12px] font-semibold text-slate-700"
                    >
                        Status
                    </label>

                    <select

                        id="createRadiusStatus"

                        name="status"

                        class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-blue-500"

                    >

                        <option value="1">
                            Aktif
                        </option>

                        <option value="0">
                            Nonaktif
                        </option>

                    </select>

                </div>

                {{-- Info --}}

                <div
                    class="rounded-xl border border-blue-100 bg-blue-50 p-4"
                >

                    <div
                        class="flex items-start gap-3"
                    >

                        <i
                            class="fa-solid fa-circle-info mt-0.5 text-blue-600"
                        ></i>

                        <p
                            class="text-[12px] leading-6 text-blue-700"
                        >
                            Radius akan menggunakan koordinat
                            <b>Lokasi Rumah</b> atau
                            <b>Lokasi GPS Terakhir</b> kendaraan ini
                            sesuai pilihan Anda.
                        </p>

                    </div>

                </div>

            </form>

            {{-- ============================================== --}}
            {{-- Footer --}}
            {{-- ============================================== --}}

            <div
                class="flex justify-end gap-3 border-t border-slate-200 px-6 py-5"
            >

                <button

                    id="cancelCreateRadius"

                    type="button"

                    class="rounded-xl border border-slate-300 px-5 py-2.5 text-[13px] font-semibold text-slate-700 transition hover:bg-slate-100"

                >

                    Batal

                </button>

                <button

                    id="saveCreateRadius"

                    type="submit"

                    form="createRadiusForm"

                    class="rounded-xl bg-blue-600 px-5 py-2.5 text-[13px] font-semibold text-white transition hover:bg-blue-700"

                >

                    <i class="fa-solid fa-floppy-disk mr-2"></i>

                    Simpan Radius

                </button>

            </div>

        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- CREATE ADMINISTRATIVE MODAL --}}
    {{-- ========================================================= --}}

    <div

        id="createAdministrativeModal"

        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm"

    >

        <div
            class="w-full max-w-2xl overflow-hidden rounded-[24px] bg-white shadow-2xl"
        >

            {{-- ============================================== --}}
            {{-- Header --}}
            {{-- ============================================== --}}

            <div
                class="flex items-center justify-between border-b border-slate-200 px-6 py-5"
            >

                <div>

                    <p
                        class="text-[10px] font-semibold uppercase tracking-[0.25em] text-emerald-600"
                    >
                        ADMINISTRATIVE
                    </p>

                    <h2
                        class="mt-2 text-[20px] font-bold text-slate-900"
                    >
                        Tambah Geofence Administratif
                    </h2>

                    <p
                        class="mt-1 text-[13px] text-slate-500"
                    >
                        Pilih wilayah administratif sebagai geofence kendaraan.
                    </p>

                </div>

                <button

                    id="closeCreateAdministrativeModal"

                    type="button"

                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 transition hover:bg-slate-200"

                >

                    <i class="fa-solid fa-xmark"></i>

                </button>

            </div>

            {{-- ============================================== --}}
            {{-- Body --}}
            {{-- ============================================== --}}

            <form

                id="createAdministrativeForm"

                class="space-y-5 p-6"

            >

                {{-- Nama --}}

                <div>

                    <label
                        class="mb-2 block text-[12px] font-semibold text-slate-700"
                    >
                        Nama Geofence
                    </label>

                    <input

                        id="createAdministrativeName"

                        name="name"

                        type="text"

                        class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none focus:border-emerald-500"

                        placeholder="Contoh : Area Bandung"

                    >

                </div>

                <input id="createAdministrativeGeojson" type="hidden">
                <input id="createAdministrativeDisplayName" type="hidden">
                <input id="createAdministrativeAreaType" type="hidden">

                {{-- Cari Wilayah --}}

                <div>

                    <label
                        class="mb-2 block text-[12px] font-semibold text-slate-700"
                    >
                        Cari Wilayah
                    </label>

                    <div class="relative">

                        <input

                            id="createAdministrativeSearch"

                            type="text"

                            autocomplete="off"

                            placeholder="Cari kecamatan atau kelurahan..."

                            class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none focus:border-emerald-500"

                        >

                        <div
                            id="createAdministrativeResult"
                            class="absolute left-0 right-0 top-full z-[100] mt-2 hidden max-h-64 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg"
                        ></div>

                    </div>

                    <p
                        id="createAdministrativeSelected"
                        class="mt-2 hidden text-[12px] font-semibold text-emerald-700"
                    ></p>

                </div>

                {{-- Status --}}

                <div>

                    <label
                        class="mb-2 block text-[12px] font-semibold text-slate-700"
                    >
                        Status
                    </label>

                    <select

                        id="createAdministrativeStatus"

                        name="status"

                        class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none focus:border-emerald-500"

                    >

                        <option value="1">
                            Aktif
                        </option>

                        <option value="0">
                            Nonaktif
                        </option>

                    </select>

                </div>

                {{-- Informasi --}}

                <div
                    class="rounded-xl border border-emerald-100 bg-emerald-50 p-4"
                >

                    <div
                        class="flex items-start gap-3"
                    >

                        <i
                            class="fa-solid fa-circle-info mt-0.5 text-emerald-600"
                        ></i>

                        <p
                            class="text-[12px] leading-6 text-emerald-700"
                        >
                            Setelah wilayah dipilih, sistem akan otomatis
                            mengambil batas administrasi (GeoJSON) sebagai
                            geofence kendaraan.
                        </p>

                    </div>

                </div>

            </form>

            {{-- ============================================== --}}
            {{-- Footer --}}
            {{-- ============================================== --}}

            <div
                class="flex justify-end gap-3 border-t border-slate-200 px-6 py-5"
            >

                <button

                    id="cancelCreateAdministrative"

                    type="button"

                    class="rounded-xl border border-slate-300 px-5 py-2.5 text-[13px] font-semibold text-slate-700 hover:bg-slate-100"

                >

                    Batal

                </button>

                <button

                    id="saveCreateAdministrative"

                    type="submit"

                    form="createAdministrativeForm"

                    class="rounded-xl bg-emerald-600 px-5 py-2.5 text-[13px] font-semibold text-white hover:bg-emerald-700"

                >

                    <i class="fa-solid fa-floppy-disk mr-2"></i>

                    Simpan Administratif

                </button>

            </div>

        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- CREATE POLYGON MODAL --}}
    {{-- ========================================================= --}}

    <div

        id="createPolygonModal"

        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm"

    >

        <div
            class="w-full max-w-2xl overflow-hidden rounded-[24px] bg-white shadow-2xl"
        >

            {{-- ============================================== --}}
            {{-- Header --}}
            {{-- ============================================== --}}

            <div
                class="flex items-center justify-between border-b border-slate-200 px-6 py-5"
            >

                <div>

                    <p
                        class="text-[10px] font-semibold uppercase tracking-[0.25em] text-violet-600"
                    >
                        POLYGON
                    </p>

                    <h2
                        class="mt-2 text-[20px] font-bold text-slate-900"
                    >
                        Tambah Geofence Poligon
                    </h2>

                    <p
                        class="mt-1 text-[13px] text-slate-500"
                    >
                        Gambar area Poligon langsung pada peta.
                    </p>

                </div>

                <button

                    id="closeCreatePolygonModal"

                    type="button"

                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 transition hover:bg-slate-200"

                >

                    <i
                        class="fa-solid fa-xmark"
                    ></i>

                </button>

            </div>

            {{-- ============================================== --}}
            {{-- Body --}}
            {{-- ============================================== --}}

            <form

                id="createPolygonForm"

                class="space-y-5 p-6"

            >

                {{-- Nama --}}

                <div>

                    <label
                        class="mb-2 block text-[12px] font-semibold text-slate-700"
                    >
                        Nama Poligon
                    </label>

                    <input

                        id="createPolygonName"

                        name="name"

                        type="text"

                        placeholder="Contoh : Gudang"

                        class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-violet-500"

                    >

                </div>

                <input id="createPolygonGeojson" type="hidden">

                {{-- Status --}}

                <div>

                    <label
                        class="mb-2 block text-[12px] font-semibold text-slate-700"
                    >
                        Status
                    </label>

                    <select

                        id="createPolygonStatus"

                        name="status"

                        class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-violet-500"

                    >

                        <option value="1">
                            Aktif
                        </option>

                        <option value="0">
                            Nonaktif
                        </option>

                    </select>

                </div>

                {{-- Informasi Poligon --}}

                <div
                    class="rounded-2xl border border-violet-100 bg-violet-50 p-5"
                >

                    <div
                        class="flex gap-4"
                    >

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100"
                        >

                            <i
                                class="fa-solid fa-draw-polygon text-violet-600"
                            ></i>

                        </div>

                        <div>

                            <h4
                                class="text-[13px] font-semibold text-violet-900"
                            >
                                Cara Membuat Poligon
                            </h4>

                            <ol
                                class="mt-3 list-decimal space-y-2 pl-4 text-[12px] leading-6 text-violet-700"
                            >

                                <li>
                                    Tekan tombol <b>Simpan Poligon</b>.
                                </li>

                                <li>
                                    Kursor peta berubah menjadi mode gambar.
                                </li>

                                <li>
                                    Klik beberapa titik pada peta.
                                </li>

                                <li>
                                    Klik titik pertama untuk menutup polygon.
                                </li>

                                <li>
                                    Poligon akan otomatis tersimpan.
                                </li>

                            </ol>

                        </div>

                    </div>

                </div>

            </form>

            {{-- ============================================== --}}
            {{-- Footer --}}
            {{-- ============================================== --}}

            <div
                class="flex justify-end gap-3 border-t border-slate-200 px-6 py-5"
            >

                <button

                    id="cancelCreatePolygon"

                    type="button"

                    class="rounded-xl border border-slate-300 px-5 py-2.5 text-[13px] font-semibold text-slate-700 transition hover:bg-slate-100"

                >

                    Batal

                </button>

                <button

                    id="saveCreatePolygon"

                    type="submit"

                    form="createPolygonForm"

                    class="rounded-xl bg-violet-600 px-5 py-2.5 text-[13px] font-semibold text-white transition hover:bg-violet-700"

                >

                    <i class="fa-solid fa-floppy-disk mr-2"></i>

                    Simpan Poligon

                </button>

            </div>

        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- DELETE GEOFENCE MODAL --}}
    {{-- ========================================================= --}}

    <div

        id="deleteGeofenceModal"

        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm"

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
                    Hapus Geofence
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

                <input

                    id="deleteGeofenceId"

                    type="hidden"

                >

                <input

                    id="deleteGeofenceType"

                    type="hidden"

                >

                <div
                    class="rounded-xl border border-slate-200 bg-slate-50 p-4"
                >

                    <p
                        class="text-[11px] uppercase tracking-[0.22em] text-slate-400"
                    >
                        Jenis
                    </p>

                    <p

                        id="deleteGeofenceTypeText"

                        class="mt-2 text-[15px] font-semibold text-slate-900"

                    >

                        -

                    </p>

                </div>

                <div
                    class="rounded-xl border border-slate-200 bg-slate-50 p-4"
                >

                    <p
                        class="text-[11px] uppercase tracking-[0.22em] text-slate-400"
                    >
                        Nama Geofence
                    </p>

                    <p

                        id="deleteGeofenceName"

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
                            Geofence akan dihapus secara permanen dari kendaraan ini.
                            Data yang telah dihapus tidak dapat dikembalikan.
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

                    id="cancelDeleteGeofence"

                    type="button"

                    class="rounded-xl border border-slate-300 px-5 py-2.5 text-[13px] font-semibold text-slate-700 transition hover:bg-slate-100"

                >

                    Batal

                </button>

                <button

                    id="confirmDeleteGeofence"

                    type="button"

                    class="rounded-xl bg-red-600 px-5 py-2.5 text-[13px] font-semibold text-white transition hover:bg-red-700"

                >

                    <i
                        class="fa-solid fa-trash mr-2"
                    ></i>

                    Hapus Geofence

                </button>

            </div>

        </div>

    </div>

</div>