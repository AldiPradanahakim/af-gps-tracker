<x-app-layout>
    <div class="max-w-xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6">
            Lengkapi Kendaraan
        </h1>

        <form method="POST" action="{{ route('vehicles.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block font-medium text-sm text-gray-700">Nama Kendaraan</label>
                <input
                    type="text"
                    name="vehicle_name"
                    value="{{ old('vehicle_name') }}"
                    class="w-full rounded border px-3 py-2"
                >
                <x-input-error :messages="$errors->get('vehicle_name')" class="mt-2" />
            </div>

            <div class="mb-4">
                <label class="block font-medium text-sm text-gray-700">Jenis Kendaraan</label>
                <select
                    name="vehicle_type"
                    class="w-full rounded border px-3 py-2"
                >
                    <option value="motor" {{ old('vehicle_type') === 'motor' ? 'selected' : '' }}>
                        Motor
                    </option>
                    <option value="mobil" {{ old('vehicle_type') === 'mobil' ? 'selected' : '' }}>
                        Mobil
                    </option>
                </select>
                <x-input-error :messages="$errors->get('vehicle_type')" class="mt-2" />
            </div>

            <div class="mb-4">
                <label class="block font-medium text-sm text-gray-700">Nomor Polisi</label>
                <input
                    type="text"
                    name="plate_number"
                    value="{{ old('plate_number') }}"
                    class="w-full rounded border px-3 py-2"
                >
                <x-input-error :messages="$errors->get('plate_number')" class="mt-2" />
            </div>

            <button class="bg-blue-600 text-white px-5 py-2 rounded">
                Simpan
            </button>
        </form>
    </div>
</x-app-layout>
