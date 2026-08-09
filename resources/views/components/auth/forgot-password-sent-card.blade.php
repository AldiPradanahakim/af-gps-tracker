@props(['email' => null])

<div class="mx-auto flex w-full max-w-[430px] h-fit self-center justify-self-center flex-col gap-[12px] rounded-[24px] bg-white p-[clamp(24px,2vw,32px)] shadow-xl border border-[#E5E7EB]">
    <div class="space-y-3 text-center">
        <div class="mx-auto flex h-[72px] w-[72px] items-center justify-center rounded-full bg-[#EFF6FF] text-[#2563EB] shadow-sm">
            <img src="{{ asset('images/logo-gps.png') }}" alt="Logo kecil Trackio" class="h-[42px] w-[42px] object-contain" />
        </div>
        <div>
            <h2 class="text-[32px] font-semibold tracking-[-0.03em]">Cek <span class="text-[#2563EB]">Email Anda</span></h2>
            <p class="mt-2 text-[14px] text-slate-500">
                Kami telah mengirimkan tautan reset kata sandi
                @if($email)
                    ke <span class="font-medium text-slate-700">{{ $email }}</span>.
                @else
                    ke email Anda.
                @endif
                Silakan buka email tersebut dan klik tombol di dalamnya untuk membuat kata sandi baru.
            </p>
        </div>
    </div>

    <p class="text-center text-[13px] text-slate-400">Tidak menemukan emailnya? Cek folder Spam/Promosi, atau tunggu beberapa saat lalu kirim ulang.</p>

    <div class="space-y-[12px]">
        <a href="{{ route('password.request') }}" class="flex h-[46px] w-full items-center justify-center rounded-xl bg-gradient-to-r from-[#2563EB] to-[#3B82F6] text-[14px] font-bold text-white shadow-xl transition duration-200 hover:brightness-95">
            Kirim Ulang Tautan
        </a>

        <p class="pt-2 text-center text-[14px] text-slate-500">
            <a href="{{ route('login') }}" class="font-medium text-[#2563EB] hover:text-[#1D4ED8]">Kembali ke halaman masuk</a>
        </p>
    </div>
</div>
