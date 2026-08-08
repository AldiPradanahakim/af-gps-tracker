<x-guest-layout>
    <x-auth.onboarding-shell
        :subtext="'Buat kata sandi baru yang kuat untuk menjaga keamanan akun Trackio Anda.'">
        <x-slot:heading>
            Amankan Kembali <span class="text-[#2563EB]">Akun Anda</span>
        </x-slot:heading>

        <form method="POST" action="{{ route('password.store') }}" class="w-full flex items-center justify-center">
            @csrf
            <x-auth.reset-password-card :request="$request" />
        </form>
    </x-auth.onboarding-shell>
</x-guest-layout>
