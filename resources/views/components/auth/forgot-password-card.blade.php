<div class="mx-auto flex w-full max-w-[26.875rem] h-fit self-center justify-self-center flex-col gap-[0.75rem] rounded-[1.5rem] bg-white p-6 sm:p-[clamp(1.5rem,2vw,2rem)] shadow-xl border border-[#E5E7EB]">
    <div class="space-y-3 text-center">
        <div class="mx-auto flex h-[4.5rem] w-[4.5rem] items-center justify-center rounded-full bg-[#EFF6FF] text-[#2563EB] shadow-sm">
            <img src="{{ asset('images/logo-gps.png') }}" alt="Logo kecil AF GPS TRACKER" class="h-[2.625rem] w-[2.625rem] object-contain" />
        </div>
        <div>
            <h2 class="text-[1.625rem] sm:text-[2rem] font-semibold tracking-[-0.03em]">Lupa <span class="text-[#2563EB]">Kata Sandi</span></h2>
            <p class="mt-2 text-[0.875rem] text-slate-500">Masukkan email akun Anda, kami akan kirim tautan untuk membuat kata sandi baru.</p>
        </div>
    </div>

    <div class="space-y-[0.75rem]">
        <div class="space-y-2">
            <label for="email" class="block text-[0.875rem] font-medium text-slate-700">Email</label>
            <div class="relative flex h-[2.875rem] items-center rounded-xl border border-slate-300 bg-white shadow-sm transition duration-200 focus-within:border-[#2563EB] focus-within:ring-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M4 4h16v16H4z" /><path d="M22 6L12 13 2 6" /></svg>
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Masukkan Email" class="h-full w-full border-0 bg-transparent pl-[2.75rem] pr-[2.75rem] text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-0 focus:border-[#2563EB]" />
            </div>
            @error('email')<p class="mt-1 text-[0.8125rem] text-red-600">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="flex h-[2.875rem] w-full items-center justify-center rounded-xl bg-gradient-to-r from-[#2563EB] to-[#3B82F6] text-[0.875rem] font-bold text-white shadow-xl transition duration-200 hover:brightness-95">
            Kirim Tautan Atur Ulang
        </button>

        <p class="pt-2 text-center text-[0.875rem] text-slate-500">
            <a href="{{ route('login') }}" class="font-medium text-[#2563EB] hover:text-[#1D4ED8]">Kembali ke halaman masuk</a>
        </p>
    </div>
</div>
