<x-guest-layout>
    <x-auth.onboarding-shell :subtext="'Hubungkan kendaraan Anda dengan perangkat GPS untuk mulai memantau lokasi secara real-time.'">
        <x-slot:heading>
            Lengkapi <span class="text-[#2563EB]">Informasi Kendaraan</span>
        </x-slot:heading>

        <x-vehicle.vehicle-card />
    </x-auth.onboarding-shell>
</x-guest-layout>
