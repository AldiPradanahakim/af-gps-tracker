<div class="flex h-full w-[360px] flex-col border-r border-slate-200 bg-white">

    {{-- HEADER --}}
    <div class="border-b border-slate-200 px-6 py-5">

        <div class="flex items-center gap-4">

            <img
                src="{{ asset('images/LOGO GPS.png') }}"
                alt="GPS Tracker"
                class="h-12 w-12 object-contain">

            <div>

                <h2 class="text-sm font-bold uppercase tracking-[0.35em] text-[#2563EB]">
                    GPS TRACKER
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Monitoring System
                </p>

            </div>

        </div>

    </div>

    {{-- TITLE --}}
    <div class="px-6 pt-6">

        <div class="flex items-center justify-between">

            <div>

                <h3 class="text-xl font-bold text-slate-900">
                    Kendaraan
                </h3>

                <p
                    id="vehicle-count"
                    class="mt-1 text-sm text-slate-500">

                    {{ $vehicles->count() }} Kendaraan Terhubung

                </p>

            </div>

            <button
                id="addVehicle"
                type="button"
                class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#2563EB] text-white shadow transition hover:bg-blue-700">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 4v16m8-8H4"/>

                </svg>

            </button>

        </div>

    </div>

    {{-- VEHICLE LIST --}}
    <div
        id="vehicle-list"
        class="mt-6 flex-1 overflow-y-auto px-5 pb-6">

        <div
            id="vehicle-container"
            class="space-y-4">

            @forelse($vehicles as $vehicle)

                <div
                    id="vehicle-{{ $vehicle['device_id'] }}"
                    class="vehicle-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-200 hover:border-[#2563EB] hover:shadow-lg"
                    data-device="{{ $vehicle['device_id'] }}"
                    data-code="{{ $vehicle['device_code'] }}"
                    data-lat="{{ $vehicle['latitude'] }}"
                    data-lng="{{ $vehicle['longitude'] }}"
                    data-selected="false">

                    <div class="flex items-start justify-between">

                        <div>

                            <h4
                                id="vehicle-name-{{ $vehicle['device_id'] }}"
                                class="text-lg font-bold text-slate-900">

                                {{ $vehicle['vehicle_name'] }}

                            </h4>

                            <p
                                id="vehicle-plate-{{ $vehicle['device_id'] }}"
                                class="mt-1 text-sm text-slate-500">

                                {{ strtoupper($vehicle['plate_number']) }}

                            </p>

                        </div>

                        @if($vehicle['is_active'])

                            <span
                                id="vehicle-status-{{ $vehicle['device_id'] }}"
                                data-status
                                data-online="1"
                                class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">

                                Online

                            </span>

                        @else

                            <span
                                id="vehicle-status-{{ $vehicle['device_id'] }}"
                                data-status
                                data-online="0"
                                class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-600">

                                Offline

                            </span>

                        @endif

                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-4">

                        <div>

                            <p class="text-xs uppercase tracking-wide text-slate-400">
                                Kecepatan
                            </p>

                            <p
                                id="vehicle-speed-{{ $vehicle['device_id'] }}"
                                data-speed
                                class="mt-1 text-base font-semibold text-slate-900">

                                {{ $vehicle['speed'] ?? 0 }} km/j

                            </p>

                        </div>

                        <div>

                            <p class="text-xs uppercase tracking-wide text-slate-400">
                                Jenis
                            </p>

                            <p
                                id="vehicle-type-{{ $vehicle['device_id'] }}"
                                data-type
                                class="mt-1 text-base font-semibold text-slate-900">

                                {{ ucfirst($vehicle['vehicle_type']) }}

                            </p>

                        </div>
                                            </div>

                    {{-- Realtime Information --}}
                    <div class="mt-5 grid grid-cols-2 gap-4">

                        <div>

                            <p class="text-xs uppercase tracking-wide text-slate-400">
                                Baterai
                            </p>

                            <p
                                id="vehicle-battery-{{ $vehicle['device_id'] }}"
                                data-battery
                                class="mt-1 text-base font-semibold text-slate-900">

                                {{ $vehicle['battery'] ?? '-' }}

                            </p>

                        </div>

                        <div>

                            <p class="text-xs uppercase tracking-wide text-slate-400">
                                Update
                            </p>

                            <p
                                id="vehicle-updated-{{ $vehicle['device_id'] }}"
                                data-updated
                                class="mt-1 text-sm font-medium text-slate-500">

                                {{ $vehicle['updated_at'] ?? '-' }}

                            </p>

                        </div>

                    </div>

                    <div class="mt-6 flex items-center gap-2">

                        @php
                            $hasCoordinate =
                                !is_null($vehicle['latitude']) &&
                                !is_null($vehicle['longitude']);
                        @endphp

                        <button
                            type="button"
                            onclick="GPSTracker.focusVehicle('{{ $vehicle['device_id'] }}')"
                            @disabled(!$hasCoordinate)
                            class="flex-1 rounded-xl px-4 py-2.5 text-sm font-semibold transition
                            {{ $hasCoordinate
                                ? 'bg-[#2563EB] text-white hover:bg-blue-700'
                                : 'bg-slate-300 text-slate-500 cursor-not-allowed' }}">

                            {{ $hasCoordinate ? 'Lihat di Peta' : 'Belum Ada GPS' }}

                        </button>

                        <a
                            href="{{ route('vehicles.show', $vehicle['device_id']) }}"
                            class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">

                            Detail

                        </a>

                    </div>

                </div>

            @empty

                <div
                    id="vehicle-empty-state"
                    class="rounded-2xl border border-dashed border-slate-300 p-8 text-center">

                    <p class="text-sm text-slate-500">
                        Belum ada kendaraan.
                    </p>

                </div>

            @endforelse

        </div>

    </div>

</div>