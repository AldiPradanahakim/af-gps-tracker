<x-guest-layout>
    <x-auth.onboarding-shell
        :subtext="'Lupa kata sandi bukan masalah. Masukkan email Anda dan kami akan mengirimkan tautan untuk membuat kata sandi baru.'">
        <x-slot:heading>
            Reset Kata Sandi Anda <span class="text-[#2563EB]">Dengan Aman</span>
        </x-slot:heading>

        @if (session('status'))
            <x-auth.forgot-password-sent-card :email="session('email')" />
        @else
            <form method="POST" action="{{ route('password.email') }}" class="w-full flex items-center justify-center">
                @csrf
                <x-auth.forgot-password-card />
            </form>
        @endif
    </x-auth.onboarding-shell>
</x-guest-layout>
