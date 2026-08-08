@props(['request'])

<div class="mx-auto flex w-full max-w-[430px] h-fit self-center justify-self-center flex-col gap-[12px] rounded-[24px] bg-white p-[clamp(24px,2vw,32px)] shadow-xl border border-[#E5E7EB]">
    <div class="space-y-3 text-center">
        <div class="mx-auto flex h-[72px] w-[72px] items-center justify-center rounded-full bg-[#EFF6FF] text-[#2563EB] shadow-sm">
            <img src="{{ asset('images/LOGO GPS.png') }}" alt="Logo kecil Trackio" class="h-[42px] w-[42px] object-contain" />
        </div>
        <div>
            <h2 class="text-[32px] font-semibold tracking-[-0.03em]">Kata Sandi <span class="text-[#2563EB]">Baru</span></h2>
            <p class="mt-2 text-[14px] text-slate-500">Buat kata sandi baru untuk akun Anda.</p>
        </div>
    </div>

    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div class="space-y-[12px]">
        <div class="space-y-2">
            <label for="email" class="block text-[14px] font-medium text-slate-700">Email</label>
            <div class="relative flex h-[46px] items-center rounded-xl border border-slate-300 bg-white shadow-sm transition duration-200 focus-within:border-[#2563EB] focus-within:ring-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M4 4h16v16H4z" /><path d="M22 6L12 13 2 6" /></svg>
                <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="Masukkan Email" class="h-full w-full border-0 bg-transparent pl-[44px] pr-[44px] text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-0 focus:border-[#2563EB]" />
            </div>
            @error('email')<p class="mt-1 text-[13px] text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="space-y-2" x-data="{ show: false }">
            <label for="password" class="block text-[14px] font-medium text-slate-700">Kata Sandi Baru</label>
            <div class="relative flex h-[46px] items-center rounded-xl border border-slate-300 bg-white shadow-sm transition duration-200 focus-within:border-[#2563EB] focus-within:ring-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input :type="show ? 'text' : 'password'" id="password" name="password" required autocomplete="new-password" placeholder="Masukkan Kata Sandi Baru" class="h-full w-full border-0 bg-transparent pl-[44px] pr-[44px] text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-0" />
                <button type="button" @click="show = !show" class="absolute right-4 text-slate-400 transition hover:text-slate-600">
                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-5 0-9.27-3.11-11-7 1.16-2.91 3.13-5.29 5.58-6.69"/><path d="M1 1l22 22"/></svg>
                </button>
            </div>
            @error('password')<p class="mt-1 text-[13px] text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="space-y-2" x-data="{ show: false }">
            <label for="password_confirmation" class="block text-[14px] font-medium text-slate-700">Konfirmasi Kata Sandi</label>
            <div class="relative flex h-[46px] items-center rounded-xl border border-slate-300 bg-white shadow-sm transition duration-200 focus-within:border-[#2563EB] focus-within:ring-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input :type="show ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi Kata Sandi Baru" class="h-full w-full border-0 bg-transparent pl-[44px] pr-[44px] text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-0" />
                <button type="button" @click="show = !show" class="absolute right-4 text-slate-400 transition hover:text-slate-600">
                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-5 0-9.27-3.11-11-7 1.16-2.91 3.13-5.29 5.58-6.69"/><path d="M1 1l22 22"/></svg>
                </button>
            </div>
            @error('password_confirmation')<p class="mt-1 text-[13px] text-red-600">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="flex h-[46px] w-full items-center justify-center rounded-xl bg-gradient-to-r from-[#2563EB] to-[#3B82F6] text-[14px] font-bold text-white shadow-xl transition duration-200 hover:brightness-95">
            Simpan Kata Sandi Baru
        </button>
    </div>
</div>
