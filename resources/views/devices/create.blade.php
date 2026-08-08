<x-guest-layout>
    <x-auth.onboarding-shell :subtext="'Masukkan ID dan kata sandi perangkat untuk menghubungkan GPS tracker ke akun Trackio Anda.'">
        <x-slot:heading>
            Aktivasi Perangkat <span class="text-[#2563EB]">GPS Anda</span>
        </x-slot:heading>

        <div class="w-full max-w-[520px] rounded-[24px] bg-white p-8 shadow-xl border border-[#E5E7EB]">
            <div class="text-center">
                <div class="mx-auto mb-4 flex h-[72px] w-[72px] items-center justify-center rounded-full bg-[#EFF6FF] shadow-sm">
                    <img src="{{ asset('images/LOGO GPS.png') }}" alt="Trackio" class="h-[42px] w-[42px] object-contain" />
                </div>
                <h1 class="text-[28px] font-semibold tracking-[-0.03em] text-slate-950">Aktivasi Perangkat</h1>
                <p class="mt-3 text-sm leading-6 text-slate-500">
                    Masukkan Device ID dan Password Device yang terdapat pada perangkat GPS Anda.
                </p>
            </div>

            @if (session('success'))
                <div class="mt-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <p class="font-semibold">Terjadi kesalahan:</p>
                    <ul class="mt-2 list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('devices.store') }}" class="mt-8 space-y-6">
                @csrf

                <div class="space-y-4">
                    <label for="device_id" class="block text-sm font-semibold text-slate-700">ID Perangkat</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7V3m0 0H8m8 0v4m0 0H8m0 0v4m0 0H5a2 2 0 00-2 2v7a2 2 0 002 2h14a2 2 0 002-2v-7a2 2 0 00-2-2h-3" />
                            </svg>
                        </span>
                        <input id="device_id" name="device_id" type="text" value="{{ old('device_id') }}" required autofocus placeholder="Masukkan ID Perangkat"
                            class="h-[52px] w-full rounded-xl border border-slate-300 bg-white pl-14 pr-4 text-sm text-slate-900 outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-[#2563EB]/20" />
                    </div>
                    @error('device_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-4">
                    <label for="device_password" class="block text-sm font-semibold text-slate-700">Kata Sandi Perangkat</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.104 0 2-.896 2-2V7a2 2 0 10-4 0v2c0 1.104.896 2 2 2zm-6 4h12m-6 0v4" />
                            </svg>
                        </span>
                        <input id="device_password" name="device_password" type="password" required autocomplete="current-password" placeholder="Masukkan Kata Sandi Perangkat"
                            class="h-[52px] w-full rounded-xl border border-slate-300 bg-white pl-14 pr-4 text-sm text-slate-900 outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-[#2563EB]/20" />
                    </div>
                    @error('device_password')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-4">
                    <button type="submit" class="flex h-[52px] w-full items-center justify-center rounded-xl bg-gradient-to-r from-[#2563EB] to-[#3B82F6] text-sm font-semibold text-white shadow-xl transition duration-200 hover:brightness-105">
                        Aktivasi
                    </button>
                    <a href="{{ route('login') }}" class="inline-flex h-[52px] w-full items-center justify-center rounded-xl border border-[#2563EB] text-sm font-semibold text-[#2563EB] transition duration-200 hover:bg-[#EFF6FF]">
                        Kembali ke Login
                    </a>
                </div>
            </form>
        </div>
    </x-auth.onboarding-shell>
</x-guest-layout>
