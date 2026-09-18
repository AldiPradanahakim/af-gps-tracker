<div class="mx-auto flex w-full max-w-[28.75rem] flex-col rounded-[1.5rem] border border-[#E5E7EB] bg-white p-5 sm:p-6 shadow-xl">

    <div class="text-center">

        <div class="mx-auto mb-4 flex h-[4.5rem] w-[4.5rem] items-center justify-center rounded-full bg-[#EFF6FF] shadow-sm">
            <img
                src="{{ asset('images/logo-gps.png') }}"
                alt="AF GPS TRACKER"
                class="h-[2.625rem] w-[2.625rem] object-contain">
        </div>

        <h2 class="text-[1.5rem] sm:text-[2.125rem] font-bold tracking-[-0.03em] text-slate-900">
            Informasi Kendaraan
        </h2>

        <p class="mt-3 text-[0.9375rem] leading-7 text-slate-500">
            Lengkapi data kendaraan yang akan dihubungkan
            dengan perangkat {{ config('app.name') }} Anda.
        </p>

    </div>

    @if ($errors->any())

        <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4">

            <ul class="space-y-1 text-sm text-red-600">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    <form
        method="POST"
        action="{{ route('vehicles.store') }}"
        class="mt-5 space-y-4">

        @csrf

        <div>

            <label class="mb-1.5 block text-sm font-semibold text-slate-700">

                Nama Kendaraan

            </label>

            <div class="relative flex h-[3.25rem] items-center rounded-xl border border-slate-300 bg-white transition duration-200 focus-within:border-[#2563EB] focus-within:ring-2 focus-within:ring-[#2563EB]/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M5 17h2m10 0h2a1 1 0 001-1v-4l-2-4H6L4 12v4a1 1 0 001 1h2m10 0a2 2 0 11-4 0m4 0a2 2 0 10-4 0m-6 0a2 2 0 11-4 0m4 0a2 2 0 10-4 0" /></svg>
                <input
                    type="text"
                    name="vehicle_name"
                    value="{{ old('vehicle_name') }}"
                    placeholder="Contoh : Motor Harian"
                    class="h-full w-full border-0 bg-transparent pl-[2.75rem] pr-4 text-sm text-slate-700 placeholder:text-slate-400 outline-none focus:outline-none focus:ring-0">
            </div>

        </div>

        <div>

            <label class="mb-1.5 block text-sm font-semibold text-slate-700">

                Nomor Polisi

            </label>

            <x-vehicle.plate-number-input
                id="plateNumberCreate"
                name="plate_number"
                :value="old('plate_number', '')"
            />

        </div>

        <div>

            <label class="mb-1.5 block text-sm font-semibold text-slate-700">

                Jenis Kendaraan

            </label>

            <select
                name="vehicle_type"
                class="dynamic-vehicle-type h-[3.25rem] w-full rounded-xl border border-slate-300 px-4 text-sm outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-[#2563EB]/20">

                <option value="">Pilih Jenis Kendaraan</option>

                <option
                    value="motor"
                    @selected(old('vehicle_type', $vehicle->vehicle_type ?? '') == 'motor')>
                    🏍 Motor
                </option>

                <option
                    value="mobil"
                    @selected(old('vehicle_type', $vehicle->vehicle_type ?? '') == 'mobil')>
                    🚗 Mobil
                </option>

                <option
                    value="kendaraan_besar"
                    @selected(old('vehicle_type', $vehicle->vehicle_type ?? '') == 'kendaraan_besar')>
                    🚚 Kendaraan Besar (Truk/Bus)
                </option>


            </select>

        </div>

        <div>

            <label class="mb-1.5 block text-sm font-semibold text-slate-700">

                Ikon Penanda

            </label>

            <select
                name="marker_icon"
                data-selected-icon="{{ old('marker_icon', $vehicle->marker_icon ?? '') }}"
                class="dynamic-marker-icon h-[3.25rem] w-full rounded-xl border border-slate-300 px-4 text-sm outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-[#2563EB]/20">
                <option value="">Pilih jenis kendaraan terlebih dahulu</option>
            </select>

        </div>

        <div>

            <label class="mb-1.5 block text-sm font-semibold text-slate-700">

                Warna Penanda

            </label>

            <select
                name="marker_color"
                class="h-[3.25rem] w-full rounded-xl border border-slate-300 px-4 text-sm outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-[#2563EB]/20">

                <option value="green">🟢 Hijau</option>

                <option value="blue">🔵 Biru</option>

                <option value="red">🔴 Merah</option>

                <option value="orange">🟠 Orange</option>

                <option value="yellow">🟡 Kuning</option>

                <option value="purple">🟣 Ungu</option>

                <option value="black">⚫ Hitam</option>

                <option value="gray">⚪ Abu-abu</option>

            </select>

        </div>

        <button
            type="submit"
            class="mt-4 flex h-[3.25rem] w-full items-center justify-center rounded-xl bg-gradient-to-r from-[#2563EB] to-[#3B82F6] text-sm font-semibold text-white shadow-lg transition hover:brightness-105">

            Simpan & Lanjutkan

        </button>

    </form>

</div>