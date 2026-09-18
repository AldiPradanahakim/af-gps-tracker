<div class="mx-auto flex w-full max-w-[26.875rem] h-fit self-center justify-self-center flex-col gap-[0.75rem] rounded-[1.5rem] bg-white p-6 sm:p-[clamp(1.5rem,2vw,2rem)] shadow-xl border border-[#E5E7EB]">
    <div class="space-y-3 text-center">
        <div class="mx-auto flex h-[4.5rem] w-[4.5rem] items-center justify-center rounded-full bg-[#EFF6FF] text-[#2563EB] shadow-sm">
            <img src="{{ asset('images/logo-gps.png') }}" alt="Logo kecil AF GPS TRACKER" class="h-[2.625rem] w-[2.625rem] object-contain" />
        </div>
        <div>
            <h2 class="text-[1.75rem] sm:text-[2.25rem] font-semibold tracking-[-0.03em]"><span class="text-[#2563EB]">Selamat</span> Datang</h2>
            <p class="mt-2 text-[0.875rem] text-slate-500">Masuk untuk memulai monitoring kendaraan.</p>
        </div>
    </div>

    <x-auth-session-status class="text-center text-sm text-green-600" :status="session('status')" />

    <div class="space-y-[0.75rem]">
        <div class="space-y-2">
            <label for="email" class="block text-[0.875rem] font-medium text-slate-700">Email</label>
            <div class="relative flex h-[2.875rem] items-center rounded-xl border border-slate-300 bg-white shadow-sm transition duration-200 focus-within:border-[#2563EB] focus-within:ring-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M4 4h16v16H4z" /><path d="M22 6L12 13 2 6" /></svg>
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Masukkan Email" class="h-full w-full border-0 bg-transparent pl-[2.75rem] pr-[2.75rem] text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-0 focus:border-[#2563EB]" />
            </div>
            @error('email')<p class="mt-1 text-[0.8125rem] text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="space-y-2" x-data="{ show: false }">
            <label for="password" class="block text-[0.875rem] font-medium text-slate-700">Kata Sandi</label>
            <div class="relative flex h-[2.875rem] items-center rounded-xl border border-slate-300 bg-white shadow-sm transition duration-200 focus-within:border-[#2563EB] focus-within:ring-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input :type="show ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password" placeholder="Masukkan Kata Sandi" class="h-full w-full border-0 bg-transparent pl-[2.75rem] pr-[2.75rem] text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-0" />
                <button type="button" @click="show = !show" class="absolute right-4 text-slate-400 transition hover:text-slate-600">
                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-5 0-9.27-3.11-11-7 1.16-2.91 3.13-5.29 5.58-6.69"/><path d="M1 1l22 22"/></svg>
                </button>
            </div>
            @error('password')<p class="mt-1 text-[0.8125rem] text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center justify-between text-[0.875rem] text-slate-600">
            <label class="inline-flex items-center gap-2">
                <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-[#D1D5DB] text-[#2563EB] focus:ring-0" />
                <span>Ingat Saya</span>
            </label>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-[0.875rem] font-medium text-[#2563EB] hover:text-[#1D4ED8]">Lupa Kata Sandi?</a>
            @endif
        </div>

        <button type="submit" class="flex h-[2.875rem] w-full items-center justify-center rounded-xl bg-gradient-to-r from-[#2563EB] to-[#3B82F6] text-[0.875rem] font-bold text-white shadow-xl transition duration-200 hover:brightness-95">
            Masuk
        </button>

        <div class="flex items-center gap-3 text-[0.875rem] text-slate-400 py-2">
            <span class="h-px flex-1 bg-[#E5E7EB]"></span>
            <span>atau</span>
            <span class="h-px flex-1 bg-[#E5E7EB]"></span>
        </div>

        <a href="{{ route('devices.create') }}" class="inline-flex h-[2.875rem] w-full items-center justify-center gap-2 rounded-xl border border-[#2563EB] bg-white text-[0.875rem] font-semibold text-[#2563EB] transition duration-200 hover:bg-[#EFF6FF]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
            Aktivasi Perangkat GPS
        </a>

        <p class="pt-2 text-center text-[0.875rem] text-slate-500">
            Belum memiliki akun?<br />
            <span class="font-medium text-slate-700">Aktivasi perangkat GPS anda terlebih dahulu.</span>
        </p>
    </div>
</div>
