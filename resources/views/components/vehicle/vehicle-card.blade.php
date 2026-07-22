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