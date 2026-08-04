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
                Setiap kendaraan hanya dapat memiliki satu Radius Geofence,
                satu Administrative Geofence, dan satu Polygon Geofence.
                Seluruh pengaturan dilakukan langsung pada halaman ini.
            </p>

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
                            Radius Geofence
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
                                    {{ number_format($radius->radius) }} Meter
                                </span>

                            </div>

                            <div class="grid grid-cols-[110px_15px_1fr]">

                                <span class="text-[12px] text-slate-500">
                                    Trigger
                                </span>

                                <span class="text-slate-400">
                                    :
                                </span>

                                <span class="font-semibold text-slate-900">
                                    {{ ucfirst($radius->trigger_type) }}
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

                                    @if($radius->is_active)

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

                                Edit

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
                            Kendaraan ini belum memiliki Radius Geofence.
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

                            value="{{ $radius->radius ?? '' }}"

                            class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-blue-500"

                        >

                    </div>

                    {{-- Trigger --}}

                    <div>

                        <label
                            class="mb-2 block text-[12px] font-medium text-slate-700"
                        >
                            Trigger
                        </label>

                        <select

                            id="editRadiusTrigger"

                            name="trigger"

                            class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none"

                        >

                            <option value="enter">
                                Enter
                            </option>

                            <option value="exit">
                                Exit
                            </option>

                            <option value="both">
                                Enter & Exit
                            </option>

                        </select>

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
                        class="rounded-xl border border-amber-200 bg-amber-50 p-4"
                    >

                        <div
                            class="flex items-start gap-3"
                        >

                            <i
                                class="fa-solid fa-circle-info mt-0.5 text-amber-600"
                            ></i>

                            <p
                                class="text-[12px] leading-6 text-amber-700"
                            >
                                Jika ingin mengubah titik pusat Radius,
                                klik <b>Ubah Titik Radius</b>,
                                kemudian pilih lokasi baru pada peta.
                            </p>

                        </div>

                    </div>

                    {{-- Action --}}

                    <div
                        class="grid grid-cols-3 gap-3 pt-2"
                    >

                        <button

                            id="changeRadiusCenter"

                            type="button"

                            class="rounded-xl border border-blue-200 bg-blue-50 py-2.5 text-[12px] font-semibold text-blue-600"

                        >

                            <i class="fa-solid fa-location-crosshairs mr-2"></i>

                            Ubah Titik

                        </button>

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
        {{-- Administrative --}}
        {{-- ===================================================== --}}

        <section

            id="administrativeGeofenceCard"

            class="overflow-hidden rounded-[20px] border border-slate-200 bg-white vehicle-panel-shadow"

        >

            {{-- ========================================================= --}}
            {{-- Administrative Card --}}
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
                            Administrative Geofence
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
                                    Provinsi
                                </span>

                                <span class="text-slate-400">
                                    :
                                </span>

                                <span class="font-semibold text-slate-900">
                                    {{ $administrative->province }}
                                </span>

                            </div>

                            <div class="grid grid-cols-[110px_15px_1fr]">

                                <span class="text-[12px] text-slate-500">
                                    Kota / Kab.
                                </span>

                                <span class="text-slate-400">
                                    :
                                </span>

                                <span class="font-semibold text-slate-900">
                                    {{ $administrative->city }}
                                </span>

                            </div>

                            <div class="grid grid-cols-[110px_15px_1fr]">

                                <span class="text-[12px] text-slate-500">
                                    Kecamatan
                                </span>

                                <span class="text-slate-400">
                                    :
                                </span>

                                <span class="font-semibold text-slate-900">
                                    {{ $administrative->district }}
                                </span>

                            </div>

                            <div class="grid grid-cols-[110px_15px_1fr]">

                                <span class="text-[12px] text-slate-500">
                                    Trigger
                                </span>

                                <span class="text-slate-400">
                                    :
                                </span>

                                <span class="font-semibold text-slate-900">
                                    {{ ucfirst($administrative->trigger_type) }}
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

                                    @if($administrative->is_active)

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

                                Edit

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
                            Administrative belum tersedia
                        </h4>

                        <p
                            class="mt-2 text-[12px] leading-6 text-slate-500"
                        >
                            Kendaraan ini belum memiliki Administrative Geofence.
                        </p>

                        <button

                            id="createAdministrativeButton"

                            type="button"

                            class="mt-6 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-[12px] font-semibold text-white transition hover:bg-emerald-700"

                        >

                            <i class="fa-solid fa-plus"></i>

                            Tambah Administrative

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

                    {{-- Nama --}}

                    <div>

                        <label
                            class="mb-2 block text-[12px] font-medium text-slate-700"
                        >
                            Nama Administrative
                        </label>

                        <input

                            id="editAdministrativeName"

                            name="name"

                            type="text"

                            value="{{ $administrative->name ?? '' }}"

                            class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-emerald-500"

                        >

                    </div>

                    {{-- Provinsi --}}

                    <div>

                        <label
                            class="mb-2 block text-[12px] font-medium text-slate-700"
                        >
                            Provinsi
                        </label>

                        <select

                            id="editAdministrativeProvince"

                            name="province"

                            class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none focus:border-emerald-500"

                        >

                            <option value="">
                                Pilih Provinsi
                            </option>

                        </select>

                    </div>

                    {{-- Kota & Kecamatan --}}

                    <div
                        class="grid grid-cols-2 gap-4"
                    >

                        <div>

                            <label
                                class="mb-2 block text-[12px] font-medium text-slate-700"
                            >
                                Kota / Kabupaten
                            </label>

                            <select

                                id="editAdministrativeCity"

                                name="city"

                                class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none focus:border-emerald-500"

                            >

                                <option value="">
                                    Pilih Kota
                                </option>

                            </select>

                        </div>

                        <div>

                            <label
                                class="mb-2 block text-[12px] font-medium text-slate-700"
                            >
                                Kecamatan
                            </label>

                            <select

                                id="editAdministrativeDistrict"

                                name="district"

                                class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none focus:border-emerald-500"

                            >

                                <option value="">
                                    Pilih Kecamatan
                                </option>

                            </select>

                        </div>

                    </div>

                    {{-- Kelurahan --}}

                    <div>

                        <label
                            class="mb-2 block text-[12px] font-medium text-slate-700"
                        >
                            Kelurahan / Desa
                        </label>

                        <select

                            id="editAdministrativeVillage"

                            name="village"

                            class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none focus:border-emerald-500"

                        >

                            <option value="">
                                Pilih Kelurahan
                            </option>

                        </select>

                    </div>

                    {{-- Trigger & Status --}}

                    <div
                        class="grid grid-cols-2 gap-4"
                    >

                        <div>

                            <label
                                class="mb-2 block text-[12px] font-medium text-slate-700"
                            >
                                Trigger
                            </label>

                            <select

                                id="editAdministrativeTrigger"

                                name="trigger"

                                class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none"

                            >

                                <option value="enter">
                                    Enter
                                </option>

                                <option value="exit">
                                    Exit
                                </option>

                                <option value="both">
                                    Enter & Exit
                                </option>

                            </select>

                        </div>

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

                                <option value="1">
                                    Aktif
                                </option>

                                <option value="0">
                                    Nonaktif
                                </option>

                            </select>

                        </div>

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
        {{-- Polygon --}}
        {{-- ===================================================== --}}

        <section

            id="polygonGeofenceCard"

            class="overflow-hidden rounded-[20px] border border-slate-200 bg-white vehicle-panel-shadow"

        >

            {{-- ========================================================= --}}
            {{-- Polygon Card --}}
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
                            Polygon Geofence
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
                                    {{ $polygon->total_point ?? '-' }}
                                </span>

                            </div>

                            {{-- Luas Area --}}

                            <div
                                class="grid grid-cols-[110px_15px_1fr]"
                            >

                                <span
                                    class="text-[12px] text-slate-500"
                                >
                                    Luas Area
                                </span>

                                <span
                                    class="text-slate-400"
                                >
                                    :
                                </span>

                                <span
                                    class="font-semibold text-slate-900"
                                >
                                    {{ $polygon->area ?? '-' }}
                                    m²
                                </span>

                            </div>

                            {{-- Trigger --}}

                            <div
                                class="grid grid-cols-[110px_15px_1fr]"
                            >

                                <span
                                    class="text-[12px] text-slate-500"
                                >
                                    Trigger
                                </span>

                                <span
                                    class="text-slate-400"
                                >
                                    :
                                </span>

                                <span
                                    class="font-semibold text-slate-900"
                                >
                                    {{ ucfirst($polygon->trigger_type) }}
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

                                    @if($polygon->is_active)

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

                                Edit

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
                            Polygon belum tersedia
                        </h4>

                        <p
                            class="mt-2 text-[12px] leading-6 text-slate-500"
                        >
                            Kendaraan ini belum memiliki Polygon Geofence.
                        </p>

                        <button

                            id="createPolygonButton"

                            type="button"

                            class="mt-6 inline-flex items-center gap-2 rounded-xl bg-violet-600 px-5 py-2.5 text-[12px] font-semibold text-white transition hover:bg-violet-700"

                        >

                            <i
                                class="fa-solid fa-plus"
                            ></i>

                            Tambah Polygon

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

                    {{-- Nama Polygon --}}

                    <div>

                        <label
                            class="mb-2 block text-[12px] font-medium text-slate-700"
                        >
                            Nama Polygon
                        </label>

                        <input

                            id="editPolygonName"

                            name="name"

                            type="text"

                            value="{{ $polygon->name ?? '' }}"

                            class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-violet-500"

                        >

                    </div>

                    {{-- Trigger & Status --}}

                    <div
                        class="grid grid-cols-2 gap-4"
                    >

                        <div>

                            <label
                                class="mb-2 block text-[12px] font-medium text-slate-700"
                            >
                                Trigger
                            </label>

                            <select

                                id="editPolygonTrigger"

                                name="trigger"

                                class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-violet-500"

                            >

                                <option value="enter">
                                    Enter
                                </option>

                                <option value="exit">
                                    Exit
                                </option>

                                <option value="both">
                                    Enter & Exit
                                </option>

                            </select>

                        </div>

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

                                <option value="1">
                                    Aktif
                                </option>

                                <option value="0">
                                    Nonaktif
                                </option>

                            </select>

                        </div>

                    </div>

                    {{-- Informasi Polygon --}}

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
                                Untuk mengubah bentuk Polygon,
                                klik tombol <strong>Ubah Area Polygon</strong>,
                                kemudian edit titik-titik Polygon langsung pada peta.
                            </p>

                        </div>

                    </div>

                    {{-- Area Polygon --}}

                    <button

                        id="changePolygonArea"

                        type="button"

                        class="flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-violet-200 bg-violet-50 text-[12px] font-semibold text-violet-600 transition hover:bg-violet-100"

                    >

                        <i
                            class="fa-solid fa-draw-polygon"
                        ></i>

                        Ubah Area Polygon

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
                        Tambah Radius Geofence
                    </h2>

                    <p
                        class="mt-1 text-[13px] text-slate-500"
                    >
                        Tambahkan Radius Geofence untuk kendaraan ini.
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

                        name="name"

                        type="text"

                        class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-blue-500"

                        placeholder="Contoh : Rumah"

                    >

                </div>

                {{-- Radius --}}

                <div>

                    <label
                        class="mb-2 block text-[12px] font-semibold text-slate-700"
                    >
                        Radius (Meter)
                    </label>

                    <input

                        name="radius"

                        type="number"

                        class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-blue-500"

                        placeholder="100"

                    >

                </div>

                {{-- Trigger --}}

                <div>

                    <label
                        class="mb-2 block text-[12px] font-semibold text-slate-700"
                    >
                        Trigger
                    </label>

                    <select

                        name="trigger"

                        class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-blue-500"

                    >

                        <option value="enter">
                            Enter
                        </option>

                        <option value="exit">
                            Exit
                        </option>

                        <option value="both">
                            Enter & Exit
                        </option>

                    </select>

                </div>

                {{-- Status --}}

                <div>

                    <label
                        class="mb-2 block text-[12px] font-semibold text-slate-700"
                    >
                        Status
                    </label>

                    <select

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
                            Setelah menekan tombol <b>Simpan</b>,
                            silakan klik pada peta untuk menentukan
                            titik pusat Radius Geofence.
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
                        Tambah Administrative Geofence
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

                        name="name"

                        type="text"

                        class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none focus:border-emerald-500"

                        placeholder="Contoh : Area Bandung"

                    >

                </div>

                {{-- Provinsi --}}

                <div>

                    <label
                        class="mb-2 block text-[12px] font-semibold text-slate-700"
                    >
                        Provinsi
                    </label>

                    <select

                        name="province"

                        class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none focus:border-emerald-500"

                    >

                        <option value="">
                            Pilih Provinsi
                        </option>

                    </select>

                </div>

                {{-- Kota & Kecamatan --}}

                <div
                    class="grid grid-cols-2 gap-4"
                >

                    <div>

                        <label
                            class="mb-2 block text-[12px] font-semibold text-slate-700"
                        >
                            Kota / Kabupaten
                        </label>

                        <select

                            name="city"

                            class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none focus:border-emerald-500"

                        >

                            <option value="">
                                Pilih Kota
                            </option>

                        </select>

                    </div>

                    <div>

                        <label
                            class="mb-2 block text-[12px] font-semibold text-slate-700"
                        >
                            Kecamatan
                        </label>

                        <select

                            name="district"

                            class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none focus:border-emerald-500"

                        >

                            <option value="">
                                Pilih Kecamatan
                            </option>

                        </select>

                    </div>

                </div>

                {{-- Kelurahan --}}

                <div>

                    <label
                        class="mb-2 block text-[12px] font-semibold text-slate-700"
                    >
                        Kelurahan / Desa
                    </label>

                    <select

                        name="village"

                        class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none focus:border-emerald-500"

                    >

                        <option value="">
                            Pilih Kelurahan
                        </option>

                    </select>

                </div>

                {{-- Trigger --}}

                <div
                    class="grid grid-cols-2 gap-4"
                >

                    <div>

                        <label
                            class="mb-2 block text-[12px] font-semibold text-slate-700"
                        >
                            Trigger
                        </label>

                        <select

                            name="trigger"

                            class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none focus:border-emerald-500"

                        >

                            <option value="enter">
                                Enter
                            </option>

                            <option value="exit">
                                Exit
                            </option>

                            <option value="both">
                                Enter & Exit
                            </option>

                        </select>

                    </div>

                    <div>

                        <label
                            class="mb-2 block text-[12px] font-semibold text-slate-700"
                        >
                            Status
                        </label>

                        <select

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

                    class="rounded-xl bg-emerald-600 px-5 py-2.5 text-[13px] font-semibold text-white hover:bg-emerald-700"

                >

                    <i class="fa-solid fa-floppy-disk mr-2"></i>

                    Simpan Administrative

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
                        Tambah Polygon Geofence
                    </h2>

                    <p
                        class="mt-1 text-[13px] text-slate-500"
                    >
                        Gambar area Polygon langsung pada peta.
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
                        Nama Polygon
                    </label>

                    <input

                        name="name"

                        type="text"

                        placeholder="Contoh : Gudang"

                        class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-violet-500"

                    >

                </div>

                {{-- Trigger --}}

                <div
                    class="grid grid-cols-2 gap-4"
                >

                    <div>

                        <label
                            class="mb-2 block text-[12px] font-semibold text-slate-700"
                        >
                            Trigger
                        </label>

                        <select

                            name="trigger"

                            class="h-11 w-full rounded-xl border border-slate-300 px-4 text-[13px] outline-none transition focus:border-violet-500"

                        >

                            <option value="enter">
                                Enter
                            </option>

                            <option value="exit">
                                Exit
                            </option>

                            <option value="both">
                                Enter & Exit
                            </option>

                        </select>

                    </div>

                    <div>

                        <label
                            class="mb-2 block text-[12px] font-semibold text-slate-700"
                        >
                            Status
                        </label>

                        <select

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

                </div>

                {{-- Informasi Polygon --}}

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
                                Cara Membuat Polygon
                            </h4>

                            <ol
                                class="mt-3 list-decimal space-y-2 pl-4 text-[12px] leading-6 text-violet-700"
                            >

                                <li>
                                    Tekan tombol <b>Simpan Polygon</b>.
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
                                    Polygon akan otomatis tersimpan.
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

                    class="rounded-xl bg-violet-600 px-5 py-2.5 text-[13px] font-semibold text-white transition hover:bg-violet-700"

                >

                    <i class="fa-solid fa-floppy-disk mr-2"></i>

                    Simpan Polygon

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