<div class="mx-auto flex w-full max-w-[460px] flex-col rounded-[24px] border border-[#E5E7EB] bg-white p-8 shadow-xl">

    <div class="text-center">

        <div class="mx-auto mb-4 flex h-[72px] w-[72px] items-center justify-center rounded-full bg-[#EFF6FF] shadow-sm">
            <img
                src="{{ asset('images/logo-gps.png') }}"
                alt="Trackio"
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

            <div class="relative flex h-[52px] items-center rounded-xl border border-slate-300 bg-white transition duration-200 focus-within:border-[#2563EB] focus-within:ring-2 focus-within:ring-[#2563EB]/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M5 17h2m10 0h2a1 1 0 001-1v-4l-2-4H6L4 12v4a1 1 0 001 1h2m10 0a2 2 0 11-4 0m4 0a2 2 0 10-4 0m-6 0a2 2 0 11-4 0m4 0a2 2 0 10-4 0" /></svg>
                <input
                    type="text"
                    name="vehicle_name"
                    value="{{ old('vehicle_name') }}"
                    placeholder="Contoh : Motor Harian"
                    class="h-full w-full border-0 bg-transparent pl-[44px] pr-4 text-sm text-slate-700 placeholder:text-slate-400 outline-none focus:outline-none focus:ring-0">
            </div>

        </div>

        <div>

            <label class="mb-2 block text-sm font-semibold text-slate-700">

                Nomor Polisi

            </label>

            <div class="relative flex h-[52px] items-center rounded-xl border border-slate-300 bg-white transition duration-200 focus-within:border-[#2563EB] focus-within:ring-2 focus-within:ring-[#2563EB]/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="5" width="18" height="14" rx="2" /><path d="M3 10h18" /><path d="M7 15h4" /></svg>
                <input
                    type="text"
                    name="plate_number"
                    value="{{ old('plate_number') }}"
                    placeholder="Contoh : D 1234 ABC"
                    class="h-full w-full border-0 bg-transparent pl-[44px] pr-4 uppercase text-sm text-slate-700 placeholder:text-slate-400 outline-none focus:outline-none focus:ring-0">
            </div>

        </div>

        <div>

            <label class="mb-2 block text-sm font-semibold text-slate-700">

                Jenis Kendaraan

            </label>

            <select
                id="vehicleType"
                name="vehicle_type"
                class="h-[52px] w-full rounded-xl border border-slate-300 px-4 text-sm outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-[#2563EB]/20">

                <option value="">Pilih Jenis Kendaraan</option>

                <option
                    value="motor"
                    @selected(old('vehicle_type') == 'motor')>

                    🏍 Motor

                </option>

                <option
                    value="mobil"
                    @selected(old('vehicle_type') == 'mobil')>

                    🚗 Mobil

                </option>

                <option
                    value="pickup"
                    @selected(old('vehicle_type') == 'pickup')>

                    🛻 Pickup

                </option>

                <option
                    value="truck"
                    @selected(old('vehicle_type') == 'truck')>

                    🚚 Truk

                </option>

                <option
                    value="bus"
                    @selected(old('vehicle_type') == 'bus')>

                    🚌 Bus

                </option>

                <option
                    value="van"
                    @selected(old('vehicle_type') == 'van')>

                    🚐 Van

                </option>

                <option
                    value="taxi"
                    @selected(old('vehicle_type') == 'taxi')>

                    🚕 Taxi

                </option>

                <option
                    value="ambulance"
                    @selected(old('vehicle_type') == 'ambulance')>

                    🚑 Ambulance

                </option>

                <option
                    value="police"
                    @selected(old('vehicle_type') == 'police')>

                    🚓 Polisi

                </option>

                <option
                    value="bicycle"
                    @selected(old('vehicle_type') == 'bicycle')>

                    🚲 Sepeda

                </option>

            </select>

        </div>

        <div>

            <label class="mb-2 block text-sm font-semibold text-slate-700">

                Icon Marker

            </label>

            <select
                id="markerIcon"
                name="marker_icon"
                class="h-[52px] w-full rounded-xl border border-slate-300 px-4 text-sm outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-[#2563EB]/20">

                <option value="motorcycle">🏍 Motor</option>

                <option value="car">🚗 Mobil</option>

                <option value="pickup">🛻 Pickup</option>

                <option value="truck">🚚 Truk</option>

                <option value="bus">🚌 Bus</option>

                <option value="van">🚐 Van</option>

                <option value="taxi">🚕 Taxi</option>

                <option value="ambulance">🚑 Ambulance</option>

                <option value="police">🚓 Polisi</option>

                <option value="bicycle">🚲 Sepeda</option>

            </select>

        </div>

        <div>

            <label class="mb-2 block text-sm font-semibold text-slate-700">

                Warna Marker

            </label>

            <select
                name="marker_color"
                class="h-[52px] w-full rounded-xl border border-slate-300 px-4 text-sm outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-[#2563EB]/20">

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
            class="mt-4 flex h-[52px] w-full items-center justify-center rounded-xl bg-gradient-to-r from-[#2563EB] to-[#3B82F6] text-sm font-semibold text-white shadow-lg transition hover:brightness-105">

            Simpan & Lanjutkan

        </button>

    </form>

</div>

<script>

document.addEventListener('DOMContentLoaded', () => {

    const vehicleType = document.getElementById('vehicleType');

    const markerIcon = document.getElementById('markerIcon');

    const mapping = {

        motor: 'motorcycle',

        mobil: 'car',

        pickup: 'pickup',

        truck: 'truck',

        bus: 'bus',

        van: 'van',

        taxi: 'taxi',

        ambulance: 'ambulance',

        police: 'police',

        bicycle: 'bicycle',

    };

    function syncMarkerIcon() {

        const icon = mapping[vehicleType.value];

        if (icon) {

            markerIcon.value = icon;

        }

    }

    vehicleType.addEventListener(

        'change',

        syncMarkerIcon

    );

    syncMarkerIcon();

});

</script>