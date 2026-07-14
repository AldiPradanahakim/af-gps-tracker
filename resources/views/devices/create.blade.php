<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center px-4 py-6 sm:px-6 lg:px-8">
        <div class="w-full max-w-[1500px] min-h-[calc(100vh-48px)] rounded-[32px] bg-[#F8FAFC] shadow-none border-0 grid gap-6 lg:grid-cols-[58%_42%] overflow-visible">
            <div class="hidden lg:flex min-h-0 h-full w-full flex-col justify-between bg-[#F8FAFC] p-6 xl:p-8">
                <div class="space-y-6 max-w-[560px]">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('images/LOGO GPS.png') }}" alt="GPS TRACKER" class="h-14 w-14 object-contain" />
                        <div class="space-y-1">
                            <div class="text-xs font-semibold uppercase tracking-[0.35em] text-[#2563EB]">GPS TRACKER</div>
                            <div class="text-xs text-slate-500">Monitoring System</div>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <h1 class="text-[clamp(34px,3vw,48px)] leading-[1.05] font-extrabold text-slate-950 tracking-[-0.03em] max-w-[560px]">
                            Aktivasi Perangkat GPS
                        </h1>
                        <p class="max-w-lg text-base leading-7 text-slate-600">
                            <span class="text-[#2563EB] font-semibold">Masukkan</span> Perangkat ID dan Kata Sandi Perangkat untuk menghubungkan GPS tracker ke akun anda.
                        </p>
                    </div>
                </div>

                <div class="flex w-full max-w-[640px] flex-col items-center justify-center">
                    <div class="w-full overflow-hidden rounded-[28px] shadow-sm bg-white p-4">
                        <img src="{{ asset('images/illustrator login.png') }}" alt="Ilustrasi GPS Tracker" class="w-full h-auto object-contain" />
                    </div>
                    <p class="mt-6 text-sm text-slate-500">© 2026 GPS Tracking System. All Rights Reserved.</p>
                </div>
            </div>

            <div class="flex min-h-0 h-full items-center justify-center bg-[#F8FAFC] p-6">
                <div class="w-full max-w-[520px] rounded-[24px] bg-white p-8 shadow-xl border border-[#E5E7EB]">
                    <div class="text-center">
                        <div class="mx-auto mb-4 flex h-[72px] w-[72px] items-center justify-center rounded-full bg-[#EFF6FF] shadow-sm">
                            <img src="{{ asset('images/LOGO GPS.png') }}" alt="GPS TRACKER" class="h-[42px] w-[42px] object-contain" />
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
            </div>
        </div>
    </div>
</x-guest-layout>
