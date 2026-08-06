{{-- ========================================================= --}}
{{-- HOME LOCATION --}}
{{-- ========================================================= --}}

<section
    class="overflow-hidden rounded-[26px] border border-slate-200 bg-white vehicle-panel-shadow"
>

    {{-- ===================================================== --}}
    {{-- Header --}}
    {{-- ===================================================== --}}

    <div
        class="flex flex-col gap-5 border-b border-slate-200 px-6 py-5 lg:flex-row lg:items-center lg:justify-between"
    >

        <div>

            <p
                class="text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-400"
            >
                HOME LOCATION
            </p>

            <h2
                class="mt-2 text-[20px] font-bold text-slate-900"
            >
                Home Location Kendaraan
            </h2>

            <p
                class="mt-2 max-w-3xl text-[13px] leading-6 text-slate-500"
            >
                Tentukan lokasi Home kendaraan sebagai titik acuan monitoring.
                Lokasi dapat dicari menggunakan pencarian alamat ataupun
                dipindahkan langsung melalui Drag & Drop marker pada peta.
            </p>

        </div>

        {{-- ============================================== --}}
        {{-- Header Action --}}
        {{-- ============================================== --}}

        <div
            id="homeLocationHeaderAction"
            class="flex items-center gap-3"
        >

            {{-- Tambah --}}

            <button

                id="createHomeLocationButton"

                type="button"

                class="{{ $homeLocation ? 'hidden' : 'inline-flex' }} h-10 items-center gap-2 rounded-xl bg-blue-600 px-5 text-[13px] font-semibold text-white transition hover:bg-blue-700"

            >

                <i class="fa-solid fa-plus"></i>

                Tambah Home Location

            </button>

            {{-- Edit --}}

            <button

                id="editHomeLocationButton"

                type="button"

                class="{{ $homeLocation ? 'inline-flex' : 'hidden' }} h-10 items-center gap-2 rounded-xl border border-slate-300 bg-white px-5 text-[13px] font-semibold text-slate-700 transition hover:border-blue-500 hover:text-blue-600"

            >

                <i class="fa-solid fa-pen"></i>

                Edit

            </button>

            {{-- Delete --}}

            <button

                id="deleteHomeLocationButton"

                type="button"

                class="{{ $homeLocation ? 'inline-flex' : 'hidden' }} h-10 items-center gap-2 rounded-xl border border-red-300 bg-white px-5 text-[13px] font-semibold text-red-600 transition hover:bg-red-50"

            >

                <i class="fa-solid fa-trash"></i>

                Hapus

            </button>

        </div>

    </div>

    {{-- ===================================================== --}}
    {{-- Read Mode --}}
    {{-- ===================================================== --}}

    <div

        id="homeLocationReadContainer"

        class="{{ $homeLocation ? 'px-6 py-6' : 'hidden px-6 py-6' }}"

    >

        <div
            class="grid grid-cols-[220px_20px_1fr] gap-y-5 text-[13px]"
        >

            {{-- ============================================== --}}
            {{-- Status --}}
            {{-- ============================================== --}}

            <div
                class="text-slate-500"
            >
                Status Home Location
            </div>

            <div
                class="text-center text-slate-400"
            >
                :
            </div>

            <div>

                @if($homeLocation)

                    <span

                        id="homeLocationStatusBadge"

                        class="inline-flex items-center gap-2 rounded-full
                        {{ $homeLocation
                            ? 'bg-emerald-50 text-emerald-600'
                            : 'bg-red-50 text-red-600'
                        }}
                        px-3 py-1 text-[12px] font-semibold"

                    >

                        <span

                            id="homeLocationStatusDot"

                            class="h-2 w-2 rounded-full
                            {{ $homeLocation
                                ? 'bg-emerald-500'
                                : 'bg-red-500'
                            }}"

                        ></span>

                        <span id="homeLocationStatusText">

                            {{ $homeLocation ? 'Sudah Ditentukan' : 'Belum Ditentukan' }}

                        </span>

                    </span>

                @else

                    <span
                        class="inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1 text-[12px] font-semibold text-red-600"
                    >

                        <span
                            class="h-2 w-2 rounded-full bg-red-500"
                        ></span>

                        Belum Ditentukan

                    </span>

                @endif

            </div>

            {{-- ============================================== --}}
            {{-- Alamat --}}
            {{-- ============================================== --}}

            <div
                class="text-slate-500"
            >
                Alamat
            </div>

            <div
                class="text-center text-slate-400"
            >
                :
            </div>

            <div

                id="homeLocationReadAddress"

                class="font-medium leading-6 text-slate-900"

            >

                {{ $homeLocation['display_name'] ?? '-' }}

            </div>

            {{-- ============================================== --}}
            {{-- Latitude --}}
            {{-- ============================================== --}}

            <div
                class="text-slate-500"
            >
                Latitude
            </div>

            <div
                class="text-center text-slate-400"
            >
                :
            </div>

            <div

                id="homeLocationReadLatitude"

                class="font-semibold text-slate-900"

            >

                {{ isset($homeLocation['lat']) ? number_format((float)$homeLocation['lat'], 6) : '-' }}

            </div>

            {{-- ============================================== --}}
            {{-- Longitude --}}
            {{-- ============================================== --}}

            <div
                class="text-slate-500"
            >
                Longitude
            </div>

            <div
                class="text-center text-slate-400"
            >
                :
            </div>

            <div

                id="homeLocationReadLongitude"

                class="font-semibold text-slate-900"

            >

                {{ isset($homeLocation['lng']) ? number_format((float)$homeLocation['lng'], 6) : '-' }}

            </div>

        </div>

        {{-- ============================================== --}}
        {{-- Information Card --}}
        {{-- ============================================== --}}

        <div
            class="mt-6 grid gap-4 md:grid-cols-3"
        >

            {{-- Marker --}}

            <div
                class="rounded-2xl border border-slate-200 bg-slate-50 p-4"
            >

                <p
                    class="text-[11px] font-medium text-slate-500"
                >
                    Marker
                </p>

                <h4

                    id="homeLocationMarkerStatus"

                    class="mt-3 text-[18px] font-bold text-slate-900"

                >

                    {{ $homeLocation ? 'Aktif' : '-' }}

                </h4>

            </div>

            {{-- Search --}}

            <div
                class="rounded-2xl border border-slate-200 bg-slate-50 p-4"
            >

                <p
                    class="text-[11px] font-medium text-slate-500"
                >
                    Search Location
                </p>

                <h4

                    id="homeLocationSearchStatus"

                    class="mt-3 text-[18px] font-bold text-slate-900"

                >

                    Tersedia

                </h4>

            </div>

            {{-- Drag & Drop --}}

            <div
                class="rounded-2xl border border-slate-200 bg-slate-50 p-4"
            >

                <p
                    class="text-[11px] font-medium text-slate-500"
                >
                    Drag Marker
                </p>

                <h4

                    id="homeLocationDragStatus"

                    class="mt-3 text-[18px] font-bold text-slate-900"

                >

                    {{ $homeLocation ? 'Aktif Saat Edit' : '-' }}

                </h4>

            </div>

        </div>

    </div>

    {{-- ===================================================== --}}
    {{-- Empty State --}}
    {{-- ===================================================== --}}

    <div

        id="homeLocationEmptyContainer"

        class="{{ $homeLocation ? 'hidden px-8 py-16' : 'px-8 py-16' }}"

    >

        <div
            class="mx-auto max-w-2xl text-center"
        >

            <div
                class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-blue-50"
            >

                <i
                    class="fa-solid fa-house-circle-check text-3xl text-blue-600"
                ></i>

            </div>

            <h3
                class="mt-6 text-[22px] font-bold text-slate-900"
            >
                Home Location Belum Ditentukan
            </h3>

            <p
                class="mx-auto mt-3 max-w-xl text-[14px] leading-7 text-slate-500"
            >
                Kendaraan ini belum memiliki Home Location.
                Tambahkan Home Location terlebih dahulu untuk
                menentukan titik acuan kendaraan.
            </p>

            <button

                id="createHomeLocationButton"

                type="button"

                class="mt-8 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-[13px] font-semibold text-white transition hover:bg-blue-700"

            >

                <i
                    class="fa-solid fa-location-dot"
                ></i>

                Tambah Home Location

            </button>

        </div>

    </div>

    {{-- ===================================================== --}}
    {{-- Edit Mode --}}
    {{-- ===================================================== --}}

    <div

        id="homeLocationEditContainer"

        class="hidden p-6"

    >

        <form

            id="homeLocationForm"

            class="space-y-6"

        >

            {{-- ============================================== --}}
            {{-- Search --}}
            {{-- ============================================== --}}

            <div>

                <label
                    class="mb-2 block text-[12px] font-semibold text-slate-700"
                >
                    Cari Lokasi
                </label>

                <div
                    class="relative"
                >

                    <input

                        id="homeLocationSearch"

                        type="text"

                        autocomplete="off"

                        value="{{ $homeLocation['display_name'] ?? '' }}"

                        placeholder="Cari alamat atau lokasi..."

                        class="h-12 w-full rounded-xl border border-slate-300 pl-11 pr-4 text-[13px] outline-none transition focus:border-blue-500"

                    >

                    <i
                        class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"
                    ></i>

                </div>

                <div

                    id="homeLocationSearchResult"

                    class="mt-2 hidden rounded-xl border border-slate-200 bg-white shadow-lg"

                ></div>

            </div>

            {{-- ============================================== --}}
            {{-- Coordinate --}}
            {{-- ============================================== --}}

            <div
                class="grid gap-5 md:grid-cols-2"
            >

                {{-- Latitude --}}

                <div>

                    <label
                        class="mb-2 block text-[12px] font-semibold text-slate-700"
                    >
                        Latitude
                    </label>

                    <input

                        id="homeLatitude"

                        name="latitude"

                        type="text"

                        readonly

                        value="{{ isset($homeLocation['lat']) ? number_format((float)$homeLocation['lat'], 6) : '' }}"

                        class="h-11 w-full rounded-xl border border-slate-300 bg-slate-50 px-4 text-[13px]"

                    >

                </div>

                {{-- Longitude --}}

                <div>

                    <label
                        class="mb-2 block text-[12px] font-semibold text-slate-700"
                    >
                        Longitude
                    </label>

                    <input

                        id="homeLongitude"

                        name="longitude"

                        type="text"

                        readonly

                        value="{{ isset($homeLocation['lng']) ? number_format((float)$homeLocation['lng'], 6) : '' }}"

                        class="h-11 w-full rounded-xl border border-slate-300 bg-slate-50 px-4 text-[13px]"

                    >

                </div>

            </div>

            {{-- ============================================== --}}
            {{-- Address --}}
            {{-- ============================================== --}}

            <div>

                <label
                    class="mb-2 block text-[12px] font-semibold text-slate-700"
                >
                    Alamat
                </label>

                <textarea

                    id="homeAddress"

                    name="address"

                    rows="4"

                    readonly

                    class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-[13px] leading-6"

                >{{ $homeLocation['display_name'] ?? '' }}</textarea>

            </div>

            {{-- ============================================== --}}
            {{-- Information --}}
            {{-- ============================================== --}}

            <div
                class="rounded-2xl border border-blue-100 bg-blue-50 p-4"
            >

                <div
                    class="flex items-start gap-3"
                >

                    <i
                        class="fa-solid fa-circle-info mt-1 text-blue-600"
                    ></i>

                    <div>

                        <h4
                            class="text-[13px] font-semibold text-blue-700"
                        >
                            Informasi
                        </h4>

                        <p
                            class="mt-2 text-[12px] leading-6 text-blue-700"
                        >
                            Marker Home Location pada peta dapat digeser
                            menggunakan <strong>Drag & Drop</strong>.
                            Ketika marker dipindahkan, Latitude,
                            Longitude dan Alamat akan diperbarui secara
                            otomatis.
                        </p>

                    </div>

                </div>

            </div>

            {{-- ============================================== --}}
            {{-- Action --}}
            {{-- ============================================== --}}

            <div
                class="flex justify-end gap-3 border-t border-slate-200 pt-5"
            >

                <button

                    id="cancelHomeLocation"

                    type="button"

                    class="rounded-xl border border-slate-300 px-5 py-2.5 text-[13px] font-semibold text-slate-700 transition hover:bg-slate-100"

                >

                    Batal

                </button>

                <button

                    id="saveHomeLocation"

                    type="submit"

                    class="rounded-xl bg-blue-600 px-5 py-2.5 text-[13px] font-semibold text-white transition hover:bg-blue-700"

                >

                    <i class="fa-solid fa-floppy-disk mr-2"></i>

                    Simpan Home Location

                </button>

            </div>

        </form>

    </div>

</section>

{{-- ========================================================= --}}
{{-- Delete Modal --}}
{{-- ========================================================= --}}

<div

    id="deleteHomeLocationModal"

    class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-900/50 backdrop-blur-[2px]"

>

    <div
        class="w-full max-w-md rounded-3xl bg-white shadow-2xl"
    >

        {{-- ============================================= --}}
        {{-- Header --}}
        {{-- ============================================= --}}

        <div
            class="border-b border-slate-200 px-6 py-5"
        >

            <div
                class="flex items-center gap-4"
            >

                <div
                    class="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-100"
                >

                    <i
                        class="fa-solid fa-trash text-xl text-red-600"
                    ></i>

                </div>

                <div>

                    <h3
                        class="text-[18px] font-bold text-slate-900"
                    >
                        Hapus Home Location
                    </h3>

                    <p
                        class="mt-1 text-[13px] text-slate-500"
                    >
                        Tindakan ini tidak dapat dibatalkan.
                    </p>

                </div>

            </div>

        </div>

        {{-- ============================================= --}}
        {{-- Body --}}
        {{-- ============================================= --}}

        <div
            class="px-6 py-6"
        >

            <p
                class="text-[14px] leading-7 text-slate-600"
            >

                Apakah Anda yakin ingin menghapus
                <strong>Home Location</strong>
                kendaraan ini?

            </p>

            <div
                class="mt-5 rounded-2xl border border-red-100 bg-red-50 p-4"
            >

                <div
                    class="flex items-start gap-3"
                >

                    <i
                        class="fa-solid fa-circle-exclamation mt-1 text-red-500"
                    ></i>

                    <p
                        class="text-[13px] leading-6 text-red-700"
                    >

                        Setelah Home Location dihapus,
                        sistem tidak lagi memiliki titik
                        acuan Home untuk kendaraan ini
                        sampai pengguna membuat Home
                        Location baru.

                    </p>

                </div>

            </div>

        </div>

        {{-- ============================================= --}}
        {{-- Footer --}}
        {{-- ============================================= --}}

        <div
            class="flex justify-end gap-3 border-t border-slate-200 px-6 py-5"
        >

            <button

                id="cancelDeleteHomeLocation"

                type="button"

                class="rounded-xl border border-slate-300 px-5 py-2.5 text-[13px] font-semibold text-slate-700 transition hover:bg-slate-100"

            >

                Batal

            </button>

            <button

                id="confirmDeleteHomeLocation"

                type="button"

                class="rounded-xl bg-red-600 px-5 py-2.5 text-[13px] font-semibold text-white transition hover:bg-red-700"

            >

                <i
                    class="fa-solid fa-trash mr-2"
                ></i>

                Hapus

            </button>

        </div>

    </div>

</div>
