@props(['subtext' => null])

<div class="hidden lg:flex h-full w-full rounded-[32px] bg-[#F8FAFC] p-6 xl:p-8">
    <div class="flex h-full w-full flex-col">
        <div class="flex-shrink-0 space-y-4 max-w-[560px]">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/LOGO GPS.png') }}" alt="Trackio" class="h-14 w-14 object-contain" />
                <div class="space-y-1">
                    <div class="text-xs font-semibold uppercase tracking-[0.35em] text-[#2563EB]">Trackio</div>
                    <div class="text-xs text-slate-500">Platform Pelacakan Kendaraan</div>
                </div>
            </div>

            <div class="space-y-3">
                <h1 class="text-[clamp(26px,2.6vw,40px)] leading-[1.15] font-extrabold text-slate-950 tracking-[-0.03em] max-w-[560px]">
                    @isset($heading)
                        {{ $heading }}
                    @else
                        Satu Platform, <span class="text-[#2563EB]">Kendali Penuh</span> Kendaraan Anda
                    @endisset
                </h1>
                <p class="max-w-lg text-[15px] leading-6 text-slate-600">
                    {{ $subtext ?? 'Trackio menghadirkan pelacakan lokasi real-time, riwayat perjalanan, dan notifikasi keamanan dalam satu platform yang mudah digunakan.' }}
                </p>
            </div>
        </div>

        <div class="mt-4 flex-1 min-h-0 mx-auto flex w-full max-w-[560px] flex-col items-center justify-center">
            <div class="h-full w-full min-h-0 overflow-hidden rounded-[28px] shadow-sm bg-white p-4 flex items-center justify-center">
                <img src="{{ asset('images/illustrator login.png') }}" alt="Ilustrasi Trackio" class="h-full w-auto max-w-full object-contain" />
            </div>
            <p class="mt-3 flex-shrink-0 text-sm text-slate-500">© {{ date('Y') }} Trackio. Seluruh hak cipta dilindungi.</p>
        </div>
    </div>
</div>
