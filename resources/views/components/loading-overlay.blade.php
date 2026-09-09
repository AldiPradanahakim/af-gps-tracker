{{--
|--------------------------------------------------------------------------
| Loading Overlay Global
|--------------------------------------------------------------------------
|
| Ditampilkan setiap kali aplikasi sedang menunggu data dari server
| (filter riwayat perjalanan, refresh, export, playback). Tanpa ini
| halaman terlihat "diam" beberapa detik dan pengguna mengira tombolnya
| tidak berfungsi.
|
| Sengaja murni CSS + satu elemen - tanpa library animasi - supaya ringan
| dan tidak menambah beban render pada halaman peta yang sudah berat.
|
| Dipakai lewat GPSLoading.show() / GPSLoading.hide(), lihat
| components/scripts/loading-overlay.blade.php.
|
--}}

<div
    id="appLoading"
    role="status"
    aria-live="polite"
    aria-hidden="true"
    class="pointer-events-none fixed inset-0 z-[999998] flex items-center justify-center opacity-0 transition-opacity duration-200">

    {{-- Latar buram --}}
    <div class="absolute inset-0 bg-white/55 backdrop-blur-[3px]"></div>

    {{-- Kartu --}}
    <div
        id="appLoadingCard"
        class="relative flex translate-y-1 scale-95 flex-col items-center gap-4 rounded-3xl bg-white px-9 py-8 shadow-2xl ring-1 ring-slate-900/5 transition-all duration-200">

        {{-- Spinner: dua cincin berlawanan arah + titik GPS di tengah --}}
        <div class="relative h-14 w-14">

            <span class="app-loading-ring absolute inset-0 rounded-full border-[3px] border-slate-200 border-t-[#2563EB]"></span>

            <span class="app-loading-ring-reverse absolute inset-[7px] rounded-full border-[3px] border-transparent border-b-[#93C5FD]"></span>

            <span class="absolute inset-0 flex items-center justify-center">
                <span class="app-loading-pulse block h-2.5 w-2.5 rounded-full bg-[#2563EB]"></span>
            </span>

        </div>

        <div class="text-center">

            <p
                id="appLoadingTitle"
                class="text-[14px] font-bold text-slate-900">
                Memuat data
            </p>

            <p
                id="appLoadingMessage"
                class="mt-1 text-[12px] text-slate-500">
                Mohon tunggu sebentar&hellip;
            </p>

        </div>

    </div>

</div>

{{--
    CSS sengaja inline di sini, BUKAN lewat @push('styles').
    Komponen ini dirender di dalam <body> layout, sedangkan
    @stack('styles') sudah terlanjur dikeluarkan di <head> -
    apa pun yang di-push dari sini tidak akan pernah ikut tercetak.
--}}
@once
<style>
    /*
    | Animasi spinner - tiga keyframe sederhana, semuanya transform/opacity
    | saja supaya dikerjakan compositor GPU dan tidak memicu layout ulang.
    */

    @keyframes appLoadingSpin {
        to { transform: rotate(360deg); }
    }

    @keyframes appLoadingSpinReverse {
        to { transform: rotate(-360deg); }
    }

    @keyframes appLoadingPulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50%      { transform: scale(0.55); opacity: 0.45; }
    }

    .app-loading-ring {
        animation: appLoadingSpin 0.9s linear infinite;
    }

    .app-loading-ring-reverse {
        animation: appLoadingSpinReverse 1.4s linear infinite;
    }

    .app-loading-pulse {
        animation: appLoadingPulse 1.2s ease-in-out infinite;
    }

    /* Overlay aktif */
    #appLoading.is-visible {
        opacity: 1;
        pointer-events: auto;
    }

    #appLoading.is-visible #appLoadingCard {
        transform: translateY(0) scale(1);
    }

    /* Hormati preferensi pengguna yang mematikan animasi. */
    @media (prefers-reduced-motion: reduce) {
        .app-loading-ring,
        .app-loading-ring-reverse,
        .app-loading-pulse {
            animation-duration: 3s;
        }

        #appLoading,
        #appLoadingCard {
            transition: none;
        }
    }
</style>
@endonce
