<div class="mx-auto flex w-full max-w-[460px] flex-col rounded-[24px] border border-[#E5E7EB] bg-white p-8 shadow-xl">

    <div class="text-center">

        <div class="mx-auto mb-4 flex h-[72px] w-[72px] items-center justify-center rounded-full bg-[#EFF6FF] shadow-sm">
            <img
                src="{{ asset('images/LOGO GPS.png') }}"
                alt="GPS TRACKER"
                class="h-[42px] w-[42px] object-contain">
        </div>

        <h2 class="text-[34px] font-bold tracking-[-0.03em] text-slate-900">
            Informasi Kendaraan
        </h2>

        <p class="mt-3 text-[15px] leading-7 text-slate-500">
            Lengkapi data kendaraan yang akan dihubungkan
            dengan perangkat GPS Tracker Anda.
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
        class="mt-8 space-y-5">

        @csrf

        <div>

            <label class="mb-2 block text-sm font-semibold text-slate-700">
                Nama Kendaraan
            </label>

            <input
                type="text"
                name="vehicle_name"
                value="{{ old('vehicle_name') }}"
                placeholder="Contoh : Motor Harian"
                class="h-[52px] w-full rounded-xl border border-slate-300 px-4 text-sm outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-[#2563EB]/20">

        </div>

        <div>

            <label class="mb-2 block text-sm font-semibold text-slate-700">
                Nomor Polisi
            </label>

            <input
                type="text"
                name="plate_number"
                value="{{ old('plate_number') }}"
                placeholder="Contoh : D 1234 ABC"
                class="h-[52px] w-full rounded-xl border border-slate-300 px-4 uppercase text-sm outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-[#2563EB]/20">

        </div>

        <div>

            <label class="mb-2 block text-sm font-semibold text-slate-700">
                Jenis Kendaraan
            </label>

            <select
                name="vehicle_type"
                class="h-[52px] w-full rounded-xl border border-slate-300 px-4 text-sm outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-[#2563EB]/20">

                <option value="">Pilih Jenis Kendaraan</option>

                <option
                    value="motor"
                    @selected(old('vehicle_type') == 'motor')>
                    Motor
                </option>

                <option
                    value="mobil"
                    @selected(old('vehicle_type') == 'mobil')>
                    Mobil
                </option>

            </select>

        </div>

        <button
            type="submit"
            class="mt-4 flex h-[52px] w-full items-center justify-center rounded-xl bg-gradient-to-r from-[#2563EB] to-[#3B82F6] text-sm font-semibold text-white shadow-lg transition hover:brightness-105">

            Simpan & Lanjutkan

        </button>

    </form>

</div>