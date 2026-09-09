<div
    id="vehicleInformationModal"
    class="fixed inset-0 z-[99999] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm">

    <div class="w-full max-w-xl rounded-3xl bg-white shadow-2xl">

        <div class="border-b border-slate-200 px-8 py-6">

            <h2 class="text-2xl font-bold">

                Informasi Kendaraan

            </h2>

            <p class="mt-1 text-sm text-slate-500">

                Lengkapi data kendaraan.

            </p>

        </div>

        <form
            id="vehicleInformationForm"
            class="space-y-6 p-8">

            <div>

                <label class="mb-2 block text-sm font-semibold">

                    Nama Kendaraan

                </label>

                <input
                    name="vehicle_name"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3">

            </div>

            <div>

                <label class="mb-2 block text-sm font-semibold">

                    Plat Nomor

                </label>

                <x-vehicle.plate-number-input
                    id="plateNumberHome"
                    name="plate_number"
                />

            </div>

            <div>

                <label class="mb-2 block text-sm font-semibold">

                    Jenis Kendaraan

                </label>

                <select
                    name="vehicle_type"
                    class="dynamic-vehicle-type w-full rounded-xl border border-slate-300 px-4 py-3">

                    <option value="motor">
                        🏍 Motor
                    </option>

                    <option value="mobil">
                        🚗 Mobil
                    </option>

                    <option value="kendaraan_besar">
                        🚚 Kendaraan Besar (Truk/Bus)
                    </option>

                </select>

            </div>

            <div>

                <label class="mb-2 block text-sm font-semibold">

                    Icon Marker

                </label>

                <select
                    name="marker_icon"
                    class="dynamic-marker-icon w-full rounded-xl border border-slate-300 px-4 py-3">
                    <!-- Opsi akan diisi oleh JavaScript di app.blade.php -->
                </select>

            </div>

            <div>

                <label class="mb-2 block text-sm font-semibold">

                    Warna Marker

                </label>

                <select
                    name="marker_color"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3">

                    <option value="green">
                        🟢 Hijau
                    </option>

                    <option value="blue">
                        🔵 Biru
                    </option>

                    <option value="red">
                        🔴 Merah
                    </option>

                    <option value="orange">
                        🟠 Orange
                    </option>

                    <option value="yellow">
                        🟡 Kuning
                    </option>

                    <option value="purple">
                        🟣 Ungu
                    </option>

                    <option value="black">
                        ⚫ Hitam
                    </option>

                    <option value="gray">
                        ⚪ Abu-abu
                    </option>

                </select>

            </div>

            <div class="flex justify-end gap-3">

                <button
                    id="cancelVehicleInformation"
                    type="button"
                    class="rounded-xl border border-slate-300 px-6 py-3">

                    Batal

                </button>

                <button
                    type="submit"
                    class="rounded-xl bg-[#2563EB] px-6 py-3 font-semibold text-white">

                    Simpan Kendaraan

                </button>

            </div>

        </form>

    </div>

</div>