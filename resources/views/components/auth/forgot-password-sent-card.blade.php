@props(['email' => null])

<div class="mx-auto flex w-full max-w-[26.875rem] h-fit self-center justify-self-center flex-col gap-[0.75rem] rounded-[1.5rem] bg-white p-[clamp(1.5rem,2vw,2rem)] shadow-xl border border-[#E5E7EB]">
    <div class="space-y-3 text-center">
        <div class="mx-auto flex h-[4.5rem] w-[4.5rem] items-center justify-center rounded-full bg-[#EFF6FF] text-[#2563EB] shadow-sm">
            <img src="{{ asset('images/logo-gps.png') }}" alt="Logo kecil AF GPS TRACKER" class="h-[2.625rem] w-[2.625rem] object-contain" />
        </div>
        <div>
            <h2 class="text-[2rem] font-semibold tracking-[-0.03em]">Cek <span class="text-[#2563EB]">Email Anda</span></h2>
            <p class="mt-2 text-[0.875rem] text-slate-500">
                Kami telah mengirimkan tautan atur ulang kata sandi
                @if($email)
                    ke <span class="font-medium text-slate-700">{{ $email }}</span>.
                @else
                    ke email Anda.
                @endif
                Silakan buka email tersebut dan klik tombol di dalamnya untuk membuat kata sandi baru.
            </p>
        </div>
    </div>

    <p class="text-center text-[0.8125rem] text-slate-400">Tidak menemukan emailnya? Cek folder Spam/Promosi, atau tunggu beberapa saat lalu kirim ulang.</p>

    <div class="space-y-[0.75rem]">
        <a href="{{ route('password.request') }}" class="flex h-[2.875rem] w-full items-center justify-center rounded-xl bg-gradient-to-r from-[#2563EB] to-[#3B82F6] text-[0.875rem] font-bold text-white shadow-xl transition duration-200 hover:brightness-95">
            Kirim Ulang Tautan
        </a>

        <p class="pt-2 text-center text-[0.875rem] text-slate-500">
            <a href="{{ route('login') }}" class="font-medium text-[#2563EB] hover:text-[#1D4ED8]">Kembali ke halaman masuk</a>
        </p>
    </div>
</div>
