<div class="w-full max-w-[460px] rounded-[24px] bg-white p-8 shadow-xl border border-[#E5E7EB]">

    <div class="text-center">

        <div class="mx-auto mb-4 flex h-[72px] w-[72px] items-center justify-center rounded-full bg-[#EFF6FF] shadow-sm">
            <img src="{{ asset('images/logo-gps.png') }}"
                alt="Trackio"
                class="h-[42px] w-[42px] object-contain">
        </div>

        <h2 class="text-[30px] font-semibold tracking-[-0.03em] text-slate-950">
            Lengkapi Profil
        </h2>

        <p class="mt-3 text-sm leading-6 text-slate-500">
            Lengkapi informasi pribadi Anda untuk melanjutkan proses aktivasi perangkat GPS.
        </p>

    </div>

    @if ($errors->any())
        <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4">

            <ul class="space-y-1 text-sm text-red-600">

                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>
    @endif

    <form method="POST"
        action="{{ route('profile.store') }}"
        class="mt-8 space-y-5">

        @csrf

        <div>

            <label class="mb-2 block text-sm font-semibold text-slate-700">
                Nama Lengkap
            </label>

            <div class="relative flex h-[52px] items-center rounded-xl border border-slate-300 bg-white transition focus-within:border-[#2563EB] focus-within:ring-2 focus-within:ring-[#2563EB]/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-4 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Masukkan nama lengkap"
                    required
                    class="h-full w-full rounded-xl border-0 bg-transparent pl-14 pr-4 text-sm outline-none focus:ring-0">
            </div>

        </div>

        <div>

            <label class="mb-2 block text-sm font-semibold text-slate-700">
                Email
            </label>

            <div class="relative flex h-[52px] items-center rounded-xl border border-slate-300 bg-white transition focus-within:border-[#2563EB] focus-within:ring-2 focus-within:ring-[#2563EB]/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-4 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M4 4h16v16H4z" /><path d="M22 6L12 13 2 6" /></svg>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Masukkan email"
                    required
                    class="h-full w-full rounded-xl border-0 bg-transparent pl-14 pr-4 text-sm outline-none focus:ring-0">
            </div>

        </div>

        <div>

            <label class="mb-2 block text-sm font-semibold text-slate-700">
                Nomor WhatsApp
            </label>

            <div class="relative flex h-[52px] items-center rounded-xl border border-slate-300 bg-white transition focus-within:border-[#2563EB] focus-within:ring-2 focus-within:ring-[#2563EB]/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-4 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone') }}"
                    placeholder="08xxxxxxxxxx"
                    required
                    class="h-full w-full rounded-xl border-0 bg-transparent pl-14 pr-4 text-sm outline-none focus:ring-0">
            </div>

        </div>

        <div x-data="{ show: false }">

            <label class="mb-2 block text-sm font-semibold text-slate-700">
                Kata Sandi
            </label>

            <div class="relative flex h-[52px] items-center rounded-xl border border-slate-300 bg-white transition focus-within:border-[#2563EB] focus-within:ring-2 focus-within:ring-[#2563EB]/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-4 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input
                    :type="show ? 'text' : 'password'"
                    name="password"
                    placeholder="Minimal 8 karakter"
                    required
                    class="h-full w-full rounded-xl border-0 bg-transparent pl-14 pr-12 text-sm outline-none focus:ring-0">
                <button type="button" @click="show = !show" class="absolute right-4 text-slate-400 transition hover:text-slate-600">
                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-5 0-9.27-3.11-11-7 1.16-2.91 3.13-5.29 5.58-6.69"/><path d="M1 1l22 22"/></svg>
                </button>
            </div>

        </div>

        <div x-data="{ show: false }">

            <label class="mb-2 block text-sm font-semibold text-slate-700">
                Konfirmasi Kata Sandi
            </label>

            <div class="relative flex h-[52px] items-center rounded-xl border border-slate-300 bg-white transition focus-within:border-[#2563EB] focus-within:ring-2 focus-within:ring-[#2563EB]/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-4 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input
                    :type="show ? 'text' : 'password'"
                    name="password_confirmation"
                    placeholder="Masukkan ulang kata sandi"
                    required
                    class="h-full w-full rounded-xl border-0 bg-transparent pl-14 pr-12 text-sm outline-none focus:ring-0">
                <button type="button" @click="show = !show" class="absolute right-4 text-slate-400 transition hover:text-slate-600">
                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-5 0-9.27-3.11-11-7 1.16-2.91 3.13-5.29 5.58-6.69"/><path d="M1 1l22 22"/></svg>
                </button>
            </div>

        </div>

        <button
            type="submit"
            class="flex h-[52px] w-full items-center justify-center rounded-xl bg-gradient-to-r from-[#2563EB] to-[#3B82F6] text-sm font-semibold text-white shadow-lg transition hover:brightness-105">

            Simpan & Lanjutkan

        </button>

    </form>

</div>
